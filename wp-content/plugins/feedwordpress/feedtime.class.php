<?php
/**
 * class FeedTime: handle common date-time formats used in feeds.
 * We will try to be as tolerant as possible of different representations, since
 * RSS Is A Fucking Mess, broken dates seem to be especially pervasive, etc.
 * Supports anything that your version of PHP's strtotime() can handle; also has
 * a built-in parser for W3C DTF, in case your PHP doesn't have that.
 *
 * Tries to parse a W3C DTF first; then falls back to strtotime(). If strtotime
 * fails due to a mystery timezone, then we will try again on local time, with
 * the timezone stripped out.
 *
 * To parse a string representation of a date-time from a feed, instantiate and
 * then pull the timestamp:
 *
 * 	$time = new FeedTime($s);
 *	if (!$time->failed()) :
 * 		$ts = $time->timestamp();
 *	else :
 *		// ...
 *	endif;
 */
class FeedTime {
	var $rep;
	var $ts;

	function FeedTime ($time) {
		$this->set($time);
	} /* FeedTime constructor */
	
	function set ($time, $recurse = false) {
		$this->rep = $time;
		$this->ts = NULL;
		if (is_numeric($time)) : // Presumably a Unix-epoch timestamp
			$this->ts = $this->rep;
		elseif (is_string($time)) :
			// First, try to parse it as a W3C date-time
			$this->ts = $this->parse_w3cdtf();
			
			if ($this->failed()) :
				// In some versions of PHP, strtotime() does not
				// support the UT timezone. But since UT is by
				// definition within 1 second of UTC, we'll just
				// convert it preemptively to avoid problems.
				$time = preg_replace(
					'/(\s)UT$/',
					'$1UTC',
					$time
				);
				$this->ts = strtotime($time);
				
				if ($this->failed()
				and preg_match('/^(.*)([+\-][0-9]+|\s+[A-Za-z]{1,3})$/', $time, $matches)) :
					// Try dropping the time zone and recurse
					$this->set($matches[1], /*recurse=*/ true);
				endif;
			endif;
		endif;
	} /* FeedTime::set() */

	function timestamp () {
		$unix = NULL;
		if (!$this->failed()) :
			$unix = $this->ts;
		endif;
		return $unix;
	} /* FeedTime::timestamp() */

	function failed () {
		return (!is_numeric($this->ts) or !$this->ts or ($this->ts <= 0));
	} /* FeedTime::failed() */
	
	/**
	 * FeedTime::parse_w3cdtf() parses a W3C date-time format date into a
	 * Unix epoch timestamp. Derived from the parse_w3cdtf function included
	 * with MagpieRSS by Kellan Elliot-McCrea <kellan@protest.net>, with
	 * modifications and bugfixes by Charles Johnson
	 * <technophilia@radgeek.com>, under the terms of the GPL.
	 */
	function parse_w3cdtf () {
		$unix = NULL; // Failure

		# regex to match wc3dtf
		$pat = "/^\s*
			  (\d{4})
			  (-
			    (\d{2})
			    (-
			      (\d{2})
			      (T
			        (\d{2})
			        :(\d{2})
			        (:
			          (\d{2})
			          (\.\d+)?
			        )?
			        (?:([-+])(\d{2}):?(\d{2})|(Z))?
			      )?
			    )?
			  )?
			  \s*\$
			/x";

		if ( preg_match( $pat, $this->rep, $match ) ) :
			$year = (isset($match[1]) ? $match[1] : NULL);
			$month = (isset($match[3]) ? $match[3] : NULL);
			$day = (isset($match[5]) ? $match[5] : NULL);
			$hours = (isset($match[7]) ? $match[7] : NULL);
			$minutes = (isset($match[8]) ? $match[8] : NULL);
			$seconds = (isset($match[10]) ? $match[10] : NULL);

			# W3C dates can omit the time, the day of the month, or even the month.
			# Fill in any blanks using information from the present moment. --CWJ
			$default['hr'] = (int) gmdate('H');
			$default['day'] = (int) gmdate('d');
			$default['month'] = (int) gmdate('m');
		
			if (is_null($hours)) : $hours = $default['hr']; $minutes = 0; $seconds = 0; endif;
			if (is_null($day)) : $day = $default['day']; endif;
			if (is_null($month)) : $month = $default['month']; endif;
		
			# calc epoch for current date assuming GMT
			$unix = gmmktime( $hours, $minutes, $seconds, $month, $day, $year);

			$offset = 0;
			if ( isset($match[15]) and $match[15] == 'Z' ) :
				# zulu time, aka GMT
			else :
				$tz_mod = (isset($match[12]) ? $match[12] : NULL);
				$tz_hour = (isset($match[13]) ? $match[13] : NULL);
				$tz_min = (isset($match[14]) ? $match[14] : NULL);

				# zero out the variables
				if ( is_null($tz_hour) ) :
					$offset = (int) get_option('gmt_offset');
					$tz_hour = abs($offset);
					$tz_mod = ((abs($offset) != $offset) ? '-' : '+');
				endif;
				if ( is_null($tz_min) ) : $tz_min = 0; endif;
		
				$offset_secs = (($tz_hour*60)+$tz_min)*60;
		    
				# is timezone ahead of GMT?  then subtract offset
				if ( $tz_mod == '+' ) :
					$offset_secs = $offset_secs * -1;
				endif;
				$offset = $offset_secs; 
			endif;
			$unix = $unix + $offset;
		endif;
		return $unix;
	} /* FeedTime::parse_w3cdtf () */
} /* class FeedTime */

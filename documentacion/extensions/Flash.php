<?php
 
/*
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *  
 * Parts of the program use the file 'Image.php' from the MediaWiki project. The respective source can be acquired from http://svn.wikimedia.org/.
 * 
 * @author <marius.treitz@i-u.de>
 * 
 * A small patch by an unknown author has been applied to fix the flashvars attribute input.
 */
 
//Extension credits that show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'Flash',
        'author' => 'Marius Treitz',
        'description' => 'Allows the display of flash movies within a wiki with the <tt>&lt;flash&gt;</tt> tag',
        'url' => 'http://www.mediawiki.org/wiki/Extension:Flash',
);
 
$wgExtensionFunctions[] = "wfFlashExtension";
 
/*
 * The Flash class generates code in order to implement a flash object.
 */
class Flash {
        /* Constructor */
        function Flash( $input ) {
                Flash::parseInput( $input ); // Parse the input
                Flash::genCode(); // Generate the final code
        }
 
        /* Parser */
        function parseInput( $input ) {
                for($pos=0; $pos<strlen($input); $pos++) { // go through all arguments
                        if($input{$pos}=='=') { // separator between command
                                //ignore '=' if the attribute is flashvars
                                //this will enable to pass query string to flash files
                                if($gotflashvars) {
                                        $this->tmp .= $input{$pos};
                                        continue;
                                }
                                $this->instr = $this->tmp;
                                $this->tmp = '';
                                //set the flag for flashvars
                                if($this->instr == 'flashvars') $gotflashvars = 1;
                        }
                        else if($input{$pos}=='|') { // separator between arguments
                                //reset the flags for other attributes
                                if($gotflashvars) $gotflashvars = 0;
                                Flash::setValue();
                                $this->tmp='';
                        } else {
                                $this->tmp .= $input{$pos};
                        }
                }
                if($this->tmp!='') Flash::setValue(); // Deal with the rest of the input string
        }
 
        /* Coordinate commands with values */
        function setValue() {
                $this->value = $this->tmp;
                $this->{$this->instr} = $this->value;
                if($this->instr=='play'|| // Whitelist of flash commands. Anything else but flash commands is ignored.
                        $this->instr=='loop'||
                        $this->instr=='quality'||
                        $this->instr=='devicefont'||
                        $this->instr=='bgcolor'||
                        $this->instr=='scale'||
                        $this->instr=='align'||
                        $this->instr=='salign'||
                        $this->instr=='base'||
                        $this->instr=='menu'||
                        $this->instr=='wmode'||
                        $this->instr=='SeamlessTabbing'||
                        $this->instr=='flashvars'||
                        $this->instr=='name'||
                        $this->instr=='id') {
                        /* Create code for <embed> and <object> */
                        if($this->instr!='id') $this->codeEmbed .= ' ' . $this->instr . '="' . $this->value . '"';
                        if($this->instr!='name') $this->codeObject .= '<param name="' . $this->instr . '" value="' . $this->value . '">';
                }
        }
 
        /* Generate big, final chunk of code */
        function genCode() {
                // Possibly malicious settings:
                $allowscriptaccess = 'false'; // allow / disallow scripts
                $swliveconnect = 'false'; // start / do not start up java
 
                // Default version Setting:
                $this->version='7,0,0,0'; // Version settings for <object>
                $this->url = $this->getTitle($this->file);//Flash::imageUrl( $this->file, $this->fromSharedDirectory ); // get Wiki internal url
 
                // if flashvars is set append to the url
                if($this->flashvars) $this->url .= $this->flashvars;
 
                /* Final Code */
                $this->code = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="' . $this->width . '" height="' . $this->height . '" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=' . $this->version . '"><param name="movie" value="' . $this->url . '">' . $this->codeObject . '<embed src="' . $this->url . '" width="' . $this->width . '" height="' . $this->height . '"' . $this->codeEmbed . ' pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"></embed></object>';
                return $this->code;
        }
        function getTitle($file) {
               $title = Title::makeTitleSafe("Image",$file);
               $img = new Image($title);
               $path = $img->getViewURL(false);
               return $path;
        }
}
function wfFlashExtension() {
        global $wgParser;
        $wgParser->setHook( "flash", "renderFlash" );
}
function renderFlash( $input ) {
        global $code;
 
        // Constructor
        $flashFile = new Flash( $input );
        $code = $flashFile->code;
 
        return $code; // send the final code to the wiki
}

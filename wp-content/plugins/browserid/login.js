/*jshint browser: true*/
/*global browserid_common: true, jQuery: true*/
(function() {
  "use strict";

  var loginType;

  // Keep track of whether the onlogout callback should be ignored. Ignoring
  // the onlogout callback prevents the user from being redirected to the
  // logout page.
  var ignoreLogout = false;


  window.browserid_login = function() {
    ignoreLogout = false;
    return authenticate("login");
  };

  window.browserid_register = function() {
    ignoreLogout = false;
    return authenticate("register");
  };

  window.browserid_comment = function() {
    ignoreLogout = true;
    // Save the form state to localStorage. This allows a new user to close
    // this tab while they are verifying and still have the comment form
    // submitted once the address is verified.
    saveState();

    return authenticate("comment");
  };

  window.browserid_logout = function() {
    ignoreLogout = false;
    navigator.id.logout();

    return false;
  };

  // If this is a comment verification, ignore the logout messages until
  // the user explicitly requests a login.
  if (document.location.hash === "#comment_verification") {
    ignoreLogout = true;

    // load the state into the form to reduce flicker. The form data may not be
    // needed, but load it anyways.
    var state = loadState();

    // If there is no state, the other window has already submitted the comment.
    // navigator.id.logout has already been called and no assertion will be
    // generated. Wait for the signal from the other window which causes a refresh.
    if (!state) return refreshWhenSubmitComplete();

    // If the comment form is submitted in the original window, the user will
    // be sitting at the top of the page. Instead, go to the submit form.
    document.location.hash = "respond";

    // login type is definitely comment, in onlogin, the comment form will
    // be submitted if the original window has not already done it. If the
    // original window has already submitted the comment, this window will wait
    // until the comment is submitted and then refresh the page and go to the
    // newly inserted comment.
    loginType = "comment";
  }

  // If the user just completed comment submission, save the hash to
  // localStorage so the other window can refresh to the new comment.
  if (sessionStorage.getItem("save_comment_hash")) {
    ignoreLogout = true;

    sessionStorage.removeItem("save_comment_hash");
    localStorage.setItem("comment_hash", document.location.hash);
  }

  // If there was an error, log the user out.
  if (browserid_common.error || jQuery("#login_error").length) {
    ignoreLogout = true;

    navigator.id.logout();
  }

  navigator.id.watch({
    loggedInUser: browserid_common.logged_in_user || null,
    onlogin: function(assertion) {
      loginType = getLoginType(loginType);
      if (loginType === "login") {
        submitLoginForm(assertion);
      }
	  else if (loginType === "register") {
        submitRegistrationForm(assertion);
	  }
      else if (loginType === "comment") {
        submitCommentForm(assertion);
      }
    },
    onlogout: function() {
      // The logout was either due to an error which must be shown or to
      // the user leaving a comment but not being logged in. Either way,
      // do not redirect the user, they are where they want to be.
      if (ignoreLogout) return;

      // There is a bug in Persona with Chrome. When a user signs in, the
      // onlogout callback is first fired. Check if a user is actually
      // signed in before redirecting to the logout URL.
      if (browserid_common.logged_in_user) {
        document.location = browserid_common.logout_redirect;
      }
    }
  });

  function getLoginType(loginType) {
	return loginType || "login";
  }

  function authenticate(type) {
    loginType = type;

    var opts = {
      siteName: browserid_common.sitename || "",
      siteLogo: browserid_common.sitelogo || ""
    };

    if (loginType === "comment") {
      // If the user is signing in for a comment and must verify, redirect to
      // with a special hash. The form will be submitted by the first page to
      // receive an onlogin. Hopefully it will be the verification page, but we
      // do not know.
      var returnTo = document.location.href
                      .replace(/http(s)?:\/\//, "")
                      .replace(document.location.host, "")
                      .replace(/#.*$/, "#comment_verification");
      opts.returnTo = returnTo;
    }

    navigator.id.request(opts);

    return false;
  }

  function submitLoginForm(assertion) {
    var rememberme = document.getElementById("rememberme");
    if (rememberme !== null)
      rememberme = rememberme.checked;

	// Since login can happen on any page, create a form and submit it manually
	// ignoring the normal sign in form.
    var form = document.createElement("form");
    form.setAttribute("style", "display: none;");
    form.method = "POST";
    form.action = browserid_common.siteurl;

    var fields = {
      browserid_assertion: assertion,
      rememberme: rememberme
    };

    if (browserid_common.login_redirect !== null)
      fields.redirect_to = browserid_common.login_redirect;

    appendFormHiddenFields(form, fields);

    jQuery("body").append(form);
    form.submit();
  }

  function submitRegistrationForm(assertion) {
	jQuery("#browserid_assertion").val(assertion);
	jQuery("#browserid_assertion").val(assertion);

    jQuery("#wp-submit").click();
  }

  function submitCommentForm(assertion) {
    // If this is a new user that is verifying their email address in a new
    // window, both the original window and this window will be trying to
    // submit the comment form. The first one wins. The other one reloads.
    var state = loadState();
    if (!state) return refreshWhenSubmitComplete();

    localStorage.removeItem("comment_state");

    var form = jQuery("#commentform");

    // Get the post_id from the dom because the postID could in theory
    // change from the original if the submission is happening in a
    // new tab after email verification.
    var post_id = jQuery("#comment_post_ID").val();

    appendFormHiddenFields(form, {
      browserid_comment: post_id,
      browserid_assertion: assertion
    });

    // Save the hash so the other window can redirect to the proper comment
    // when everything has completed.
    localStorage.removeItem("comment_hash");
    sessionStorage.setItem("save_comment_hash", true);

    // If the user is submitting a comment and is not logged in,
    // log them out of Persona. This will prevent the plugin from
    // trying to log the user in to the site once the comment is posted.
    if (!browserid_common.logged_in_user) {
      ignoreLogout = true;
      navigator.id.logout();
    }

    jQuery("#submit").click();
  }

  function appendFormHiddenFields(form, fields) {
    form = jQuery(form);

    for (var name in fields) {
      var field = document.createElement("input");
      field.type = "hidden";
      field.name = name;
      field.value = fields[name];
      form.append(field);
    }
  }

  function saveState() {
    var state = {
      author: jQuery("#author").val(),
      url: jQuery("#url").val(),
      comment: jQuery("#comment").val(),
      comment_parent: jQuery("#comment_parent").val()
    };

    localStorage.setItem("comment_state", JSON.stringify(state));
  }

  function loadState() {
    var state = localStorage.getItem("comment_state");

    if (state) {
      state = JSON.parse(state);
      jQuery("#author").val(state.author);
      jQuery("#url").val(state.url);
      jQuery("#comment").val(state.comment);
      jQuery("#comment_parent").val(state.comment_parent);
    }

    return state;
  }

  function refreshWhenSubmitComplete() {
    // wait until the other window has completed the comment submit. When it
    // completes, it will store the hash of the comment that this window should
    // show.
    var hash = localStorage.getItem("comment_hash");
    if (hash) {
      localStorage.removeItem("comment_hash");
      document.location.hash = hash;
      document.location.reload(true);
    }
    else {
      setTimeout(refreshWhenSubmitComplete, 100);
    }
  }

}());

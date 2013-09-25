/*jshint browser: true*/
/*global browserid_common, jQuery*/
(function() {
  "use strict";

  var $ = jQuery;

  // what login type is being handled?
  var loginType;

  // When onlogin is invoked and there is no login state, should submit be forced?
  var forceSubmit;

  // Keep track of whether the onlogout callback should be ignored. Ignoring
  // the onlogout callback prevents the user from being redirected to the
  // logout page.
  var ignoreLogout = false;

  // Disable the registration form submit until after an assertion has been
  // returned. This allows users to press "enter" in the username field and
  // have the Persona disalog displayed
  var enableRegistrationSubmit = false;

  // If the user is trying to submit the form before they have received
  // a Persona assertion, prevent the form from being submitted. This takes
  // effect if the user types "enter" into one of the commentor info fields.
  var enableCommentSubmit = browserid_common.loggedInUser || false;

  var state;

  $(".js-persona__login").click(function(event) {
    event.preventDefault();

    ignoreLogout = false;
    requestAuthentication("login");
  });

  // the js-persona__logout button in the admin toolbar is added after this
  // script is run. Attach a live event (yuck) so that the user is still
  // able to log out.
  liveEvent(".js-persona__logout", "click", function(event) {
    event.preventDefault();

    ignoreLogout = false;
    navigator.id.logout();
  });

  if (browserid_common.isPersonaUsedWithComments) {
    $("body").addClass("persona--comments");

    $(".js-persona__submit-comment").click(function(event) {
      event.preventDefault();
      $("#commentform").submit();
    });

    $("#commentform").submit(function(event) {
      // Make sure there is a comment before submitting
      if ($("#comment").hasClass("disabled")) {
        event.preventDefault();
        return;
      }

      // If the user is trying to submit the form before they have received
      // a Persona assertion, prevent the form from being submitted and instead
      // open the Persona dialog. This takes effect if the user types "enter"
      // into one of the commentor info fields.
      if (!enableCommentSubmit) {
        event.preventDefault();

        verifyUserForComment();
      }
    });
  }

  if (browserid_common.isPersonaOnlyAuth) {
    $("body").addClass("persona--persona-only-auth");

    // Make sure there is a username before submitting
    enableSubmitWhenValid("#user_login", ".js-persona__register");

    $(".js-persona__register").click(function(event) {
      event.preventDefault();

      if ($(event.target).hasClass("disabled")) return;

      ignoreLogout = false;
      // Save the form state to localStorage. This allows a new user to close
      // this tab while they are verifying and still have the registration
      // complete once the address is verified.
      saveRegistrationState();
      requestAuthentication("register");
    });

    $("#registerform").submit(function(event) {
      // form submission is disabled so the user can press enter in the username
      // field and see the Persona dialog. After an assertion has been generated,
      // submission is re-enabled and data should be sent to the server.
      if (enableRegistrationSubmit) return;

      event.preventDefault();

      // If the username has no length, abort
      if ($("#user_login").val().length === 0) return;

      ignoreLogout = false;
      // Save the form state to localStorage. This allows a new user to close
      // this tab while they are verifying and still have the registration
      // complete once the address is verified.
      saveRegistrationState();
      requestAuthentication("register");
    });
  }

  if (document.location.hash === "#submit_comment") {
    // During comment submission, ignore the logout messages until
    // the user explicitly requests a login.
    ignoreLogout = true;

    showWaitingScreen();

    // load the state into the form to reduce flicker. The form data may not be
    // needed, but load it anyways.
    state = loadCommentState();

    // If there is no state, the other window has already submitted the comment.
    // navigator.id.logout has already been called and no assertion will be
    // generated. wait for the signal from the other window and refresh the page
    // to view the newly inserted comment.
    if (!state) return refreshWhenCommentSubmitComplete();

    loginType = "comment";

    // If this is the post Persona verification page AND we got the state
    // before the other page, forceSubmit. loadCommentState removes the
    // comment state from localStorage which normally causes onlogin to
    // abort all action.
    forceSubmit = true;
  }
  else if (document.location.hash === "#submit_registration") {
    ignoreLogout = true;

    showWaitingScreen();

    // load the state into the form to reduce flicker. The form data may not be
    // needed, but load it anyways.
    state = loadRegistrationState();

    // If there is no state, the other window has already submitted the registration.
    // Wait for the signal from the other window which causes a refresh. When
    // the signal comes, refresh to the profile page.
    if (!state) return refreshWhenRegistrationSubmitComplete();

    loginType = "register";

    // If this is the post Persona verification page AND we got the state
    // before the other page, forceSubmit. loadRegistrationState removes the
    // comment state from localStorage which normally causes onlogin to
    // abort all action.
    forceSubmit = true;
  }
  else if ((document.location.href === browserid_common.urlRegistrationRedirect) &&
           (sessionStorage.getItem("submitting_registration"))) {
    // If the user lands on the urlRegistrationRedirect page AND they just came
    // from the Registration page, inform other pages that registration has
    // completed so they can redirect.
    localStorage.setItem("registration_complete", "true");
  }
  else if (sessionStorage.getItem("submitting_comment")) {
    ignoreLogout = true;

    // If the user just completed comment submission, save the hash to
    // localStorage so the other window can refresh to the new comment.
    // We are just assuming the comment submission was successful.
    sessionStorage.removeItem("submitting_comment");
    localStorage.setItem("comment_hash", document.location.hash);
  }



  // If there was on error, log the user out.
  if (browserid_common.msgError || $("#login_error").length) {
    ignoreLogout = true;

    navigator.id.logout();
  }



  var loginHandlers = {
    login: submitLoginForm,
    register: submitRegistrationForm,
    comment: submitCommentForm
  };

  navigator.id.watch({
    loggedInUser: browserid_common.loggedInUser || null,
    onlogin: function(assertion) {
      loginType = getLoginType(loginType);

      var handler = loginHandlers[loginType];
      if (handler) {
        handler(assertion);
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
      if (browserid_common.loggedInUser) {
        document.location = browserid_common.urlLogoutRedirect;
      }
    }
  });

  function getLoginType(loginType) {
    return loginType || "login";
  }


  function requestAuthentication(type) {
    loginType = type;

    var opts = {
      siteName: browserid_common.siteName || "",
      siteLogo: browserid_common.siteLogo || "",
      backgroundColor: browserid_common.backgroundColor || "",
      termsOfService: browserid_common.termsOfService || "",
      privacyPolicy: browserid_common.privacyPolicy || ""
    };

   /**
    * If the user is signing in to comment or signing up as a new member
    * and must verify, redirect with a special hash. The form will be
    * submitted by the first page to receive an onlogin.
    *
    * This behavior is necessary because we are unsure whether the user
    * will complete verification in the original window or in a new window.
    */
    if (loginType === "comment") {
      opts.returnTo = getReturnToUrl("#submit_comment");
    }
    else if (loginType === "register") {
      opts.returnTo = getReturnToUrl("#submit_registration");
    }

    navigator.id.request(opts);
  }






  /**
   * LOGIN CODE
   */
  function submitLoginForm(assertion) {
    var rememberme = document.getElementById("rememberme");
    if (rememberme !== null)
      rememberme = rememberme.checked;

    // Since login can happen on any page, create a form
    // and submit it manually ignoring the normal sign in form.
    var form = document.createElement("form");
    form.setAttribute("style", "display: none;");
    form.method = "POST";
    form.action = browserid_common.urlLoginSubmit;

    var fields = {
      browserid_assertion: assertion,
      rememberme: rememberme
    };

    // XXX is this necessary? Won't it be fetched from options?
    if (browserid_common.urlLoginRedirect !== null)
      fields.redirect_to = browserid_common.urlLoginRedirect;

    appendFormHiddenFields(form, fields);

    $("body").append(form);
    form.submit();
  }







  /**
   * COMMENT CODE
   */

  function verifyUserForComment() {
    ignoreLogout = true;
    // Save the form state to localStorage. This allows a new user to close
    // this tab while they are verifying and still have the comment form
    // submitted once the address is verified.
    saveCommentState();
    requestAuthentication("comment");
  }

  function submitCommentForm(assertion) {
    // If this is a new user that is verifying their email address in a new
    // window, both the original window and this window will be trying to
    // submit the comment form. The first one wins. The other one reloads.
    var state = loadCommentState();
    if (!(state || forceSubmit)) return refreshWhenCommentSubmitComplete();

    var form = $("#commentform");

    // Get the post_id from the dom because the postID could in theory
    // change from the original if the submission is happening in a
    // new tab after email verification.
    var post_id = $("#comment_post_ID").val();

    appendFormHiddenFields(form, {
      browserid_comment: post_id,
      browserid_assertion: assertion
    });

    // Save the hash so the other window can redirect to the proper comment
    // when everything has completed.
    localStorage.removeItem("comment_hash");
    sessionStorage.setItem("submitting_comment", "true");

    // If the user is submitting a comment and is not logged in,
    // log them out of Persona. This will prevent the plugin from
    // trying to log the user in to the site once the comment is posted.
    if (!browserid_common.loggedInUser) {
      ignoreLogout = true;
      navigator.id.logout();
    }

    // Allow the form submission to send data to the server.
    enableCommentSubmit = true;

    $("#submit").click();
  }

  function saveCommentState() {
    var state = {
      author: $("#author").val(),
      url: $("#url").val(),
      comment: $("#comment").val(),
      comment_parent: $("#comment_parent").val()
    };

    localStorage.setItem("comment_state", JSON.stringify(state));
  }

  function loadCommentState() {
    var state = localStorage.getItem("comment_state");

    if (state) {
      state = JSON.parse(state);
      $("#author").val(state.author);
      $("#url").val(state.url);
      $("#comment").val(state.comment);
      $("#comment_parent").val(state.comment_parent);
      localStorage.removeItem("comment_state");
    }

    return state;
  }

  function refreshWhenCommentSubmitComplete() {
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
      setTimeout(refreshWhenCommentSubmitComplete, 100);
    }
  }






  /**
   * REGISTRATION CODE
   */

  function submitRegistrationForm(assertion) {
    // If this is a new user that is verifying their email address in a new
    // window, both the original window and this window will be trying to
    // submit the comment form. The first one wins. The other one reloads.
    var state = loadRegistrationState();
    if (!(state || forceSubmit)) return refreshWhenRegistrationSubmitComplete();

    // Save an item on sessionStorage that says we are completing registration.
    // When the page lands on the registration_complete redirect, it will check
    // sessionStorage, and if submitting_registration is set, it will notify
    // any other windows that registration has completed by setting a bit in
    // localStorage.
    sessionStorage.setItem("submitting_registration", "true");
    $("#browserid_assertion").val(assertion);

    // Allow the form submission to send data to the server.
    enableRegistrationSubmit = true;
    $("#wp-submit").click();
  }

  function saveRegistrationState() {
    var state = {
      user_login: $("#user_login").val()
    };

    localStorage.setItem("registration_state", JSON.stringify(state));
  }

  function loadRegistrationState() {
    var state = localStorage.getItem("registration_state");

    if (state) {
      state = JSON.parse(state);
      $("#user_login").val(state.user_login);
      localStorage.removeItem("registration_state");
    }

    return state;
  }

  function refreshWhenRegistrationSubmitComplete() {
    // wait until the other window has completed the registration submit. When it
    // completes, it will store a bit in localStorage when registration has
    // completed.
    var complete = localStorage.getItem("registration_complete");
    if (complete) {
      localStorage.removeItem("registration_complete");
      document.location = browserid_common.urlRegistrationRedirect;
    }
    else {
      setTimeout(refreshWhenRegistrationSubmitComplete, 100);
    }
  }





  /**
   * HELPER CODE
   */
  function getReturnToUrl(hash) {
    return document.location.href
               .replace(/http(s)?:\/\//, "")
               .replace(document.location.host, "")
               .replace(/#.*$/, '') + hash;
  }

  function appendFormHiddenFields(form, fields) {
    form = $(form);

    for (var name in fields) {
      var field = document.createElement("input");
      field.type = "hidden";
      field.name = name;
      field.value = fields[name];
      form.append(field);
    }
  }

  function showWaitingScreen() {
    var waitingScreen = $("<div class='persona__submit'><div class='persona__submit_spinner'></div></div>");
    $("body").append(waitingScreen);
  }

  function enableSubmitWhenValid(textField, submitButton) {
    $(submitButton).addClass("disabled");
    $(textField).keyup(validate);
    $(textField).change(validate);

    function validate() {
      var val = $(textField).val();
      // only submit val form if there is a val.
      if (val && val.trim().length) {
        $(submitButton).removeClass("disabled");
      }
      else {
        $(submitButton).addClass("disabled");
      }
    }
  }

  function liveEvent(selector, eventType, callback) {
    $("body")[typeof $.fn.on === "function" ? "on" : "delegate"](eventType, selector, callback);
  }



}());

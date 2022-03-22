/**
 * DO NOT EDIT THIS FILE.
 * See the following change record for more information,
 * https://www.drupal.org/node/2815083
 * @preserve
 **/

(function (Drupal) {
  Drupal.theme.passwordConfirmMessage = function (_ref) {
    var confirmTitle = _ref.confirmTitle;
    var confirmTextWrapper = '<span data-drupal-selector="password-match-status-text"></span>';
    return "<div aria-live=\"polite\" aria-atomic=\"true\" class=\"password-confirm-message js-password-confirm-message\" data-drupal-selector=\"password-confirm-message\">".concat(confirmTitle, " ").concat(confirmTextWrapper, "</div>");
  };

  Drupal.theme.passwordStrength = function (_ref2) {
    var strengthTitle = _ref2.strengthTitle;
    var strengthIndicator = '<div class="password-strength__indicator js-password-strength__indicator" data-drupal-selector="password-strength-indicator"></div>';
    var strengthText = '<span class="password-strength__text js-password-strength__text" data-drupal-selector="password-strength-text"></span>';
    return "\n      <div class=\"password-strength\">\n        <div class=\"password-strength__meter\" data-drupal-selector=\"password-strength-meter\">".concat(strengthIndicator, "</div>\n        <div aria-live=\"polite\" aria-atomic=\"true\" class=\"password-strength__title\">").concat(strengthTitle, " ").concat(strengthText, "</div>\n      </div>\n    ");
  };
})(Drupal);

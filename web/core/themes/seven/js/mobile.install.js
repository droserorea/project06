/**
 * DO NOT EDIT THIS FILE.
 * See the following change record for more information,
 * https://www.drupal.org/node/2815083
 * @preserve
 **/

(function () {
  function findActiveStep(steps) {
    for (var i = 0; i < steps.length; i++) {
      if (steps[i].className === 'is-active') {
        return i + 1;
      }
    }

    if (steps[steps.length - 1].className === 'done') {
      return steps.length;
    }

    return 0;
  }

  function installStepsSetup() {
    var steps = document.querySelectorAll('.task-list li');

    if (steps.length) {
      var header = document.querySelector('header[role="banner"]');
      var stepIndicator = document.createElement('div');
      stepIndicator.className = 'step-indicator';
      stepIndicator.innerHTML = "".concat(findActiveStep(steps), "/").concat(steps.length);
      header.appendChild(stepIndicator);
    }
  }

  if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', installStepsSetup);
  }
})();

if (typeof window.hypothesisConfig === 'function') {
  // hypothesisConfig has already been set. We must extend it, but since there is only
  // one option at the moment we know that highlights are on.
  window.hypothesisConfig = function () {
    return {
      showHighlights: true,
      openSidebar: true
    }
  }
} else {
  window.hypothesisConfig = function () {
    return {
      openSidebar: true
    }
  }
}

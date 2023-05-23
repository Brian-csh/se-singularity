//show a toast at the top center
function showToast(message, type) {
    toastr.options = {
      closeButton: true,
      progressBar: false,
      positionClass: 'toast-top-center',
      preventDuplicates: false,
      showDuration: 300,
      hideDuration: 1000,
      timeOut: 5000,
      extendedTimeOut: 1000,
      showEasing: 'swing',
      hideEasing: 'linear',
      showMethod: 'fadeIn',
      hideMethod: 'fadeOut'
    };
  
    toastr[type](message);
  }
  
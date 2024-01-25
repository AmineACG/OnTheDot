const successMessage = document.getElementById('success-message');

// Add a class to hide the message after 5 seconds
setTimeout(function() {
  successMessage.style.opacity = 0;
  setTimeout(function() {
    successMessage.parentNode.removeChild(successMessage);
  }, 2000);
}, 5000);
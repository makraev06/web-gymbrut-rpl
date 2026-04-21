document.addEventListener("DOMContentLoaded", function () {
  const trigger = document.querySelector("[data-toast-trigger]");
  const toast = document.querySelector(".toast-demo");
  if (trigger && toast) {
    trigger.addEventListener("click", function () {
      toast.classList.remove("d-none");
      setTimeout(() => toast.classList.add("d-none"), 2200);
    });
  }
});

function validateSignupForm(event) {
  event.preventDefault(); // Stop auto submission

  const name = document.getElementById("signupName").value.trim();
  const email = document.getElementById("signupEmail").value.trim();
  const role = document.getElementById("signupRole").value.trim();
  const phone = document.getElementById("signupPhone").value.trim();
  const address = document.getElementById("signupAddress").value.trim();
  const password = document.getElementById("signupPassword").value.trim();
  const confirm = document.getElementById("signupConfirm").value.trim();

  if (!name || !email || !role || !phone || !address || !password || !confirm) {
    alert("All fields are required!");
    return false;
  }

  if (password !== confirm) {
    alert("Passwords do not match!");
    return false;
  }

  //  Everything is valid — submit form
  event.target.submit();
}

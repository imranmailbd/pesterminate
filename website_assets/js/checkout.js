let stripe, stripe_elements, stripe_payment_element;

window.addEventListener("DOMContentLoaded", () => {
  checkBranches();

  document
    .getElementById("submitBtn")
    .addEventListener("click", async function (event) {
      event.preventDefault();
      this.disabled = true;
      this.querySelector(".spinner").classList.remove("hidden");
      const { error } = await stripe_elements.submit();
      if (!error) {
        const response = await stripe.createPaymentMethod(
          "card",
          stripe_payment_element
        );
        if (!response.error) {
          this.querySelector(".spinner").classList.add("hidden");
          this.querySelector("#buttonText").innerHTML = "Payment info saved";

          confirmCheckOut(
            1,
            document.getElementById("paymentIntentId").value,
            response.paymentMethod.id
          );
        } else {
          this.disabled = false;
          alert("payment process unsuccessful");
        }
      }
    });
});

function checkBranches() {
  // alert('check branch')

  $("#error_form").hide().html("");
  $("#firstStep .step-details").slideDown("fast");
  $("#secondStep .step-details").slideUp("slow");
  $("#thirdStep .step-details").slideUp("slow");

  let branches_id = 0;
  if (document.querySelectorAll(".radioBtn").length > 0) {
    document.querySelectorAll(".radioBtn").forEach((oneRadioObj) => {
      if (oneRadioObj.checked) {
        branches_id = oneRadioObj.value;
      }
    });
  }

  branches_expct_date = "";
  if (document.querySelectorAll("#expct_date").length > 0) {
    let branches_expct_date = document.querySelector("#expct_date").value;
    sessionStorage.setItem("branches_expct_date", branches_expct_date);
    // alert(branches_expct_date);
  }

  // sessionStorage.removeItem('branches_expct_date');

  if (branches_id == 0) {
    // $("#error_form").slideDown('fast').html('Missing Pickup Location.');
    setTimeout(function () {
      $("#error_form").slideUp("slow").html("");
    }, 4000);
    document.querySelectorAll(".radioBtn")[0].focus();

    if (!document.querySelector("#firstStep").classList.contains("allow")) {
      document.querySelector("#firstStep").classList.add("allow");
    }
    if (document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.remove("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }

    return false;
  }
  $("#error_form").hide().html("");
  const options = {
    method: "POST",
    body: JSON.stringify({ branches_id }),
    headers: { "Content-Type": "application/json" },
  };
  fetch("/set_sessionBranchesId", options)
    .then((response) => response.json())
    .then((data) => {
      if (data.branches_id > 0) {
        if (data.error != "") {
          $("#error_form").html(data.error).slideDown("fast");
          //setTimeout(function() {$("#error_form").slideUp('slow').html('');}, 5000);
        }
        checkRegistered();
      }
    });
}

function checkPhone(field_id, valid10) {
  var contactNo = field_id.value;
  if (valid10 == 1) {
    var ValidChars = "0123456789";
  } else {
    var ValidChars = " ()-+.0123456789";
  }
  var IsNumber = true;
  var Char;
  var validint = "";
  for (var i = 0; i < contactNo.length && IsNumber == true; i++) {
    Char = contactNo.charAt(i);
    if (ValidChars.indexOf(Char) == -1) {
    } else {
      validint = validint + Char;
    }
  }
  if (contactNo.length > validint.length) {
    field_id.value = validint;
  }
}

function ValidateEmail(mail) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
    return true;
  }
  return false;
}

function checkRegistered(msgShow = 0) {
  $("#firstStep .step-details").slideUp("fast");
  $("#secondStep .step-details").slideDown("slow");
  $("#thirdStep .step-details").slideUp("slow");

  // alert(sessionStorage.getItem("branches_expct_date"));

  let branches_id = 0;
  if (document.querySelectorAll(".radioBtn").length > 0) {
    document.querySelectorAll(".radioBtn").forEach((oneRadioObj) => {
      if (oneRadioObj.checked) {
        branches_id = oneRadioObj.value;
      }
    });
  }

  if (branches_id == 0) {
    checkBranches();
    return false;
  }

  // alert(sessionStorage.getItem("branches_expct_date"));
  // if (sessionStorage.getItem("branches_expct_date") !== null) {
  //     let branches_expct_date = JSON.parse(sessionStorage.getItem("branches_expct_date"));

  // }

  let nameObj = document.getElementById("name");
  let firstNameErrorObj = document.getElementById("error_name");
  firstNameErrorObj.innerHTML = "";
  if (nameObj.value == "") {
    if (msgShow == 1) {
      firstNameErrorObj.innerHTML = "Missing Name.";
    }
    nameObj.focus();
    if (!document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.add("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }
    return false;
  } else if (nameObj.value.length < 4) {
    if (msgShow == 1) {
      firstNameErrorObj.innerHTML = "Name should be min 4 characters.";
    }
    nameObj.focus();
    if (!document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.add("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }
    return false;
  } else if (nameObj.value.length > 50) {
    if (msgShow == 1) {
      firstNameErrorObj.innerHTML = "Name should be max 50 characters.";
    }
    nameObj.focus();
    if (!document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.add("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }
    return false;
  }

  let phone_numberObj = document.getElementById("phone_number");
  let phoneErrorObj = document.getElementById("error_phone_number");
  if (phone_numberObj.value.length < 9 || phone_numberObj.value.length > 15) {
    phoneErrorObj.innerHTML = "Invalid Phone No.";
    if (!document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.add("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }
    return false;
  }

  // let expct_dateObj = document.getElementById("expct_date");
  // let expctErrorObj = document.getElementById("error_expct_date");
  // if(expct_dateObj.value.length<1 || expct_dateObj.value.length>12){
  //     expctErrorObj.innerHTML = 'Invalid Expected Date';
  //     if(!document.querySelector("#firstStep").classList.contains('allow')){
  //         document.querySelector("#firstStep").classList.add('allow');
  //     }
  //     // if(document.querySelector("#thirdStep").classList.contains('allow')){
  //     //     document.querySelector("#thirdStep").classList.remove('allow');
  //     // }
  //     return false;
  // }

  let emailObj = document.getElementById("email");
  let emailErrorObj = document.getElementById("error_email");
  emailErrorObj.innerHTML = "";
  if (emailObj.value == "") {
    if (msgShow == 1) {
      emailErrorObj.innerHTML = "Missing Email Address.";
    }
    emailObj.focus();
    if (!document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.add("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }
    return false;
  } else if (emailObj.value.length < 6) {
    if (msgShow == 1) {
      emailErrorObj.innerHTML = "Email address should be min 6.";
    }
    emailObj.focus();
    if (!document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.add("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }
    return false;
  } else if (emailObj.value.length > 50) {
    if (msgShow == 1) {
      emailErrorObj.innerHTML = "Email address should be max 50 characters.";
    }
    emailObj.focus();
    if (!document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.add("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }
    return false;
  } else if (ValidateEmail(emailObj.value) == false) {
    if (msgShow == 1) {
      emailErrorObj.innerHTML = "You have entered an invalid email address!";
    }
    emailObj.focus();
    if (!document.querySelector("#secondStep").classList.contains("allow")) {
      document.querySelector("#secondStep").classList.add("allow");
    }
    if (document.querySelector("#thirdStep").classList.contains("allow")) {
      document.querySelector("#thirdStep").classList.remove("allow");
    }
    return false;
  }

  let clientSecret = document.getElementById("clientSecret").value;
  let paymentIntentId = document.getElementById("paymentIntentId").value;

  let jsonData = {
    branches_id,
    clientSecret,
    paymentIntentId,
    name: nameObj.value,
    phone_number: phone_numberObj.value,
    email: emailObj.value,
    amount: Number(document.getElementById("grandTotalPrice").value),
  };
  $("body").append(
    '<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>'
  );

  const options = {
    method: "POST",
    body: JSON.stringify(jsonData),
    headers: { "Content-Type": "application/json" },
  };
  fetch("/checkRegistered", options)
    .then((response) => response.json())
    .then((data) => {
      if (
        ["Add", "Old"].includes(data.savemsg) &&
        data.stripe_client_secret != ""
      ) {
        if (
          !document.querySelector("#secondStep").classList.contains("allow")
        ) {
          document.querySelector("#secondStep").classList.add("allow");
        }

        document.getElementById("clientSecret").value =
          data.stripe_client_secret;
        document.getElementById("paymentIntentId").value = data.paymentIntentId;

        if (data.message != "") {
          $("#error_form").html(data.message);
          return false;
        }

        checkPaymentMethod();
      } else if (data.savemsg == "error") {
        $("#error_form").slideDown("fast").html(data.message);
        setTimeout(function () {
          $("#error_form").slideUp("slow").html("");
        }, 4000);
      }
      if ($(".disScreen").length) {
        $(".disScreen").remove();
      }
    });
}

function checkPaymentMethod(msgShow = 0) {
  $("#firstStep .step-details").slideUp("fast");
  $("#secondStep .step-details").slideUp("slow");
  $("#thirdStep .step-details").slideDown("slow");

  if (!document.querySelector("#firstStep").classList.contains("allow")) {
    document.querySelector("#firstStep").classList.add("allow");
  }
  if (!document.querySelector("#secondStep").classList.contains("allow")) {
    document.querySelector("#secondStep").classList.add("allow");
  }
  if (document.querySelector("#thirdStep").classList.contains("allow")) {
    document.querySelector("#thirdStep").classList.remove("allow");
  }

  let branches_id = 0;
  if (document.querySelectorAll(".radioBtn").length > 0) {
    document.querySelectorAll(".radioBtn").forEach((oneRadioObj) => {
      if (oneRadioObj.checked) {
        branches_id = oneRadioObj.value;
      }
    });
  }

  if (branches_id == 0) {
    checkBranches();
    return false;
  }

  let newData = {};
  if (sessionStorage.getItem("cartsData") !== null) {
    newData = JSON.parse(sessionStorage.getItem("cartsData"));
  }
  if (Object.keys(newData).length == 0) {
    if (msgShow == 1) {
      $("#error_form")
        .slideDown("fast")
        .html("You have to add at least one Product into Cart.");
      setTimeout(function () {
        $("#error_form").slideUp("slow").html("");
      }, 4000);
    }
    return false;
  }

  if (!document.querySelector("#thirdStep").classList.contains("allow")) {
    document.querySelector("#thirdStep").classList.add("allow");
  }

  if (branches_id) {
    let stripe_pk = stripe_pkData[branches_id];
    stripe = Stripe(stripe_pk);

    stripe_elements = stripe.elements({
      clientSecret: document.getElementById("clientSecret").value,
    });
    stripe_payment_element = stripe_elements.create("payment");
    stripe_payment_element.mount(document.getElementById("paymentElement"));
    stripe_payment_element.on("ready", () => {
      document.getElementById("submitBtn").disabled = false; // Make submit button clickable
    });
  }
  return false;
}

function confirmCheckOut(msgShow = 0, paymentIntentId, paymentMethodId) {
  // alert('test2')
  let branches_id = 0;
  if (document.querySelectorAll(".radioBtn").length > 0) {
    document.querySelectorAll(".radioBtn").forEach((oneRadioObj) => {
      if (oneRadioObj.checked) {
        branches_id = oneRadioObj.value;
      }
    });
  }

  if (branches_id == 0) {
    checkBranches();
    return false;
  }

  let newData = {};
  if (sessionStorage.getItem("cartsData") !== null) {
    newData = JSON.parse(sessionStorage.getItem("cartsData"));
  }
  if (Object.keys(newData).length == 0) {
    if (msgShow == 1) {
      $("#error_form")
        .slideDown("fast")
        .html("You have to add at least one Product into Cart.");
      setTimeout(function () {
        $("#error_form").slideUp("slow").html("");
      }, 4000);
    }
    return false;
  }

  let subTotalPrice = document.getElementById("subTotalPrice").value;
  let service_fee = document.getElementById("service_fee").value;
  let tax1 = document.getElementById("tax1").value;
  let grandTotalPrice = document.getElementById("grandTotalPrice").value;
  let name = document.getElementById("name").value;
  let phone_number = document.getElementById("phone_number").value;
  let expct_date = sessionStorage.getItem("branches_expct_date"); //document.getElementById("expct_date").value;
  let email = document.getElementById("email").value;

  let jsonData = JSON.stringify({
    cartsData: JSON.parse(sessionStorage.getItem("cartsData")),
    paymentIntentId,
    paymentMethodId,
    subTotalPrice,
    grandTotalPrice,
    service_fee,
    tax1,
    name,
    phone_number,
    expct_date,
    email,
  });

  $("body").append(
    '<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>'
  );

  fetch("/confirmCheckOut/", {
    method: "POST",
    body: jsonData,
    headers: { "Content-Type": "application/json" },
  })
    .then((jsonResponse) => jsonResponse.json())
    .then(function (data) {
      if (data.savemsg == "Added" && data.pos_id > 0) {
        sessionStorage.setItem("pos_id", data.pos_id);
        sessionStorage.removeItem("customersdata");
        sessionStorage.removeItem("cartsData");

        if (msgShow == 1) {
          $("#success_form").html(data.message).slideDown("fast");
          $(".proceed-button").slideUp("fast");
          setTimeout(function () {
            $("#success_form").html("").slideUp("slow");
            window.location = "/My_Order";
          }, 5000);
        }
        window.location = "/My_Order.html";
      } else if (data.savemsg == "error") {
        $("#error_form").slideDown("fast").html(data.message);
        setTimeout(function () {
          $("#error_form").slideUp("slow").html("");
        }, 4000);
      }
      if ($(".disScreen").length) {
        $(".disScreen").remove();
      }
    })
    .catch(function () {
      connection_dialog(checkPaymentMethod, msgShow);
    });
}

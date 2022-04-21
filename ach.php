<!DOCTYPE html>
<html>
  <head>
    <link href="./styles/app.css" rel="stylesheet" />
    <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
    <script>
      const appId = 'sandbox-sq0idb-28OuU1COr-wx0UuFMMTTLg';
      const locationId = 'LY0FNVW3P7EF6';

      async function initializeCard(payments) {
        const card = await payments.card();
        await card.attach('#card-container');
        document.getElementById('loader-card').style.display = "none";
        return card;
      }

      async function initializeACH(payments) {
        const ach = await payments.ach();
        // Note: ACH does not have an .attach(...) method
        // the ACH auth flow is triggered by .tokenize(...)
        return ach;
      }

      async function createPayment(token, amount) {
        const body = JSON.stringify({
          locationId,
          sourceId: token,
          amount : amount
        });

        const paymentResponse = await fetch('payment-process.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body,
        });

        if (paymentResponse.ok) {
          return paymentResponse.json();
        }

        const errorBody = await paymentResponse.text();
        throw new Error(errorBody);
      }

      async function tokenize(paymentMethod, options = {}) {
        const tokenResult = await paymentMethod.tokenize(options);
        if (tokenResult.status === 'OK') {
          return tokenResult.token;
        } else {
          let errorMessage = `Tokenization failed with status: ${tokenResult.status}`;
          if (tokenResult.errors) {
            errorMessage += ` and errors: ${JSON.stringify(
              tokenResult.errors
            )}`;
          }

          throw new Error(errorMessage);
        }
      }

      // status is either SUCCESS or FAILURE;
      function displayPaymentResults(status) {
        const statusContainer = document.getElementById(
          'payment-status-container'
        );
        if (status === 'SUCCESS') {
          statusContainer.classList.remove('is-failure');
          statusContainer.classList.add('is-success');
        } else {
          statusContainer.classList.remove('is-success');
          statusContainer.classList.add('is-failure');
        }

        statusContainer.style.visibility = 'visible';
      }

      function getBillingContact(form) {
        const formData = new FormData(form);
        // It is expected that the developer performs form field validation
        // which does not occur in this example.
        return {
          givenName: formData.get('givenName'),
          familyName: formData.get('familyName'),
        };
      }

      function getACHOptions(form) {
        const billingContact = getBillingContact(form);
        const accountHolderName = `${billingContact.givenName} ${billingContact.familyName}`;

        return { accountHolderName };
      }

      document.addEventListener('DOMContentLoaded', async function () {
        if (!window.Square) {
          throw new Error('Square.js failed to load properly');
        }

        let payments;
        try {
          payments = window.Square.payments(appId, locationId);
        } catch {
          const statusContainer = document.getElementById(
            'payment-status-container'
          );
          statusContainer.className = 'missing-credentials';
          statusContainer.style.visibility = 'visible';
          return;
        }

        let card;
        try {
          card = await initializeCard(payments);
        } catch (e) {
          console.error('Initializing Card failed', e);
          return;
        }

        let ach;
        try {
          ach = await initializeACH(payments);
        } catch (e) {
          console.error('Initializing ACH failed', e);
          return;
        }

        async function handlePaymentMethodSubmission(
          event,
          paymentMethod,
          options
        ) {
          event.preventDefault();

          try {
            // disable the submit button as we await tokenization and make a payment request.
            cardButton.disabled = true;
            achButton.disabled = true;

            const amount = document.getElementById('amount').value;

            const token = await tokenize(paymentMethod, options);
            const paymentResults = await createPayment(token, amount);
            displayPaymentResults('SUCCESS');

            console.debug('Payment Success', paymentResults);
          } catch (e) {
            cardButton.disabled = false;
            achButton.disabled = false;
            displayPaymentResults('FAILURE');
            console.error(e.message);
          }
        }

        const cardButton = document.getElementById('card-button');
        cardButton.addEventListener('click', async function (event) {
          await handlePaymentMethodSubmission(event, card);
        });

        const achButton = document.getElementById('ach-button');
        achButton.addEventListener('click', async function (event) {
          const paymentForm = document.getElementById('payment-form');
          const achOptions = getACHOptions(paymentForm);
          await handlePaymentMethodSubmission(event, ach, achOptions);
        });
      });
    </script>

    <style>

        .payment{
            width: 100%;
            min-height: calc(100vh - 100px);
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .methodes-tabs{
            width: 500px;
        }

        .methodes-tabs .tabs-header {
            display: flex;
            width: 100%;
            gap:10px
        }

        .tabs-header .tab{
            padding: 10px;
            background-color: #fff;
            cursor: pointer;
        }

        .tabs-header .tab.active{
            background-color: #DD3333;
            color: #fff;
            transition: background-color 0.5s;
        }

        .tabs-content{
            width: 100%;
            min-height: 350px;
            background-color: #f9f9f9;
            padding: 30px 20px;
            border-radius: 0px 5px 5px;
            box-shadow: 2px 2px 5px 2px #f0f0f0;
            border: 1px solid #ededed;
        }

        .tab-content{
            display: none;
        }

        .tab-content.active{
            display: block;
        }

        #card-button{
            margin-top: -10px !important;
        }






        #loader-card{
          display: flex;
          align-items: center;
          justify-content: center;
          height: 137px !important;
        }
    </style>
  </head>
  <body>

    

    <div class="payment">

            <div id="payment-status-container"></div>

            <div class="methodes-tabs">

                <div class="tabs-header">
                    <div class="tab active" data-tab="tab1"><span>PAY WITH CARD</span></div>
                    <div class="tab" data-tab="tab2"><span>PAY WITH ACH</span></div>
                </div>

                <div class="tabs-content">

                    <!-- tab card method -->
                    <div class="tab-content active" id="tab1">
                        <form id="payment-form-card">

                            <input name="amount" id="amount" value="3000" type="hidden" />

                            <fieldset class="buyer-inputs">
                                <input type="text" placeholder="Name" name="name" required="required" />
                                <input type="text" placeholder="Family Name" name="lastname" required="required" />
                            </fieldset>

                            <fieldset class="buyer-inputs">
                                <input type="email" placeholder="Email" name="email" required="required" />
                            </fieldset>

                            <div id="card-container">
                              <div id="loader-card">
                                <span>Loading card form...</span>
                              </div>
                            </div>

                           

                            <button id="card-button" type="button">Pay $1.00</button>

                        </form>

                    </div>

                    <!-- tab ach method -->
                    <div class="tab-content" id="tab2">
                        <form id="payment-form">

                            <input name="amount" id="amount" value="3000" type="hidden" />
                            <br/>
                            <br/>
                            <br/>
                            <fieldset class="buyer-inputs">
                                <input
                                    type="text"
                                    autocomplete="given-name"
                                    aria-required="true"
                                    aria-label="First Name"
                                    required="required"
                                    placeholder="Given Name"
                                    name="givenName"
                                    spellcheck="false"
                                    />

                                <input
                                    type="text"
                                    autocomplete="family-name"
                                    aria-required="true"
                                    aria-label="Last Name"
                                    required="required"
                                    placeholder="Family Name"
                                    name="familyName"
                                    spellcheck="false"
                                    />
                            </fieldset>

                            <fieldset class="buyer-inputs">
                                <input type="email" placeholder="Email" name="email" required="required" />
                            </fieldset>

                            <button id="ach-button" type="button">Pay with Bank Account</button>
                        </form>
                    </div>

                </div>

            </div>


            
    </div>

    

      

    
   


    <!-- JQuery -->
    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
    <script>

        $(".tab").click(function(){
            var tab = $(this).attr("data-tab");
            $(".tab").removeClass("active");
            $(this).addClass("active");

            $(".tab-content").removeClass("active");
            $("#"+tab).addClass("active")
        })
        
    </script>
  </body>
</html>
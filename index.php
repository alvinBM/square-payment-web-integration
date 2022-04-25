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
        return card;
      }

      async function createPayment(token, amount) {
        const body = JSON.stringify({
          locationId,
          sourceId: token,
          amount: amount 
        });

        

        const paymentResponse = await fetch('payment-process.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body,
        });

        if (paymentResponse.ok) {
          console.log("payment OK", paymentResponse);
          return paymentResponse.json();
        }

        const errorBody = await paymentResponse.text();
        throw new Error(errorBody);
      }

      async function tokenize(paymentMethod) {
        const tokenResult = await paymentMethod.tokenize();
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

        // Checkpoint 2.
        async function handlePaymentMethodSubmission(event, paymentMethod) {
          event.preventDefault();

          const amount = document.getElementById('amount').value;

          try {
            // disable the submit button as we await tokenization and make a payment request.
            cardButton.disabled = true;
            const token = await tokenize(paymentMethod);
            const paymentResults = await createPayment(token, amount);
            displayPaymentResults('SUCCESS');

            console.debug('Payment Success', paymentResults);
          } catch (e) {
            cardButton.disabled = false;
            displayPaymentResults('FAILURE');
            console.error(e.message);
          }
        }

        const cardButton = document.getElementById('card-button');
        cardButton.addEventListener('click', async function (event) {
          await handlePaymentMethodSubmission(event, card);
        });
      });
    </script>
  </head>
  <body style="background-color:  #f1f1f1">
    <div style="width: 500px; margin-top: 100px; margin-left: auto; margin-right: auto">
      <form id="payment-form">
        <h1>Test square payment with card</h1>
        <input type="number" id="amount" name="amount" />
        <div id="card-container"></div>
        <button id="card-button" type="button">Pay $1.00</button>
      </form>
      <div id="payment-status-container"></div>
    </div>
    <script>
        //Notification objects have a close() method. SOME browser automatically close them.
        //Notification Events - click, error, close, show
        if( 'Notification' in window){
            
            if (Notification.permission === 'granted') {
                // If it's okay let's create a notification
                doNotify();
            }else{
                //notification == denied
                Notification.requestPermission()
                    .then(function(result) {
                        console.log(result);  //granted || denied
                        if( Notification.permission == 'granted'){ 
                            doNotify();
                        }
                    })
                    .catch( (err) => {
                        console.log(err);
                    });
            }
        
        }
        
        function doNotify(){
            let title = "The Title";
            let t = Date.now() + 30000;    //2 mins in future
            let options = {
                body: 'You have a new request on Live support',
                data: {prop1:123, prop2:"Alvin"},
                lang: 'fr-FR',
                icon: './ok_icon.png',
                timestamp: t,
                vibrate: [100, 200, 100]
            }
            let n = new Notification(title, options);

            n.addEventListener('show', function(ev){
                console.log('SHOW', ev.currentTarget.data);
            });
            n.addEventListener('close', function(ev){
               console.log('CLOSE', ev.currentTarget.body); 
            });
            setTimeout( n.close.bind(n), 6000); //close notification after 3 seconds
        }
        /*************
        Note about actions param - used with webworkers/serviceworkers
        actions: [
           {action: 'mail', title: 'e-mail', icon: './img/envelope-closed-lg.png'},
           {action: 'blastoff', title: 'Blastoff', icon: './img/rocket-lg.png'}]
       *********************/
    </script>
  </body>
</html>
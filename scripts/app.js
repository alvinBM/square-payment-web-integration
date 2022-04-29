function main() {
  const permission = document.getElementById("push-permission");
  if (
    !permission ||
    !("Notification" in window) ||
    !"ServiceWorker" in navigator
  ) {
    console.log("Vous n'avez pas le service de Notification");
    return;
  }

  const button = document.createElement("button");
  button.innerText = "Recevoir les notifications";
  permission.appendChild(button);

  button.addEventListener("click", askPermission);
}

async function askPermission() {
  const permission = await Notification.requestPermission();
  if (permission === "granted") {
    registerServiceWorker();
  }
}

async function registerServiceWorker() {
  const registration = await navigator.serviceWorker.register("sw.js");
  let subscription = await registration.pushManager.getSubscription();
  // L'utilisateur n'est pas déjà abonné, on l'abonne au notification push
  if (!subscription) {
    subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: "BAPZ1BFHrRDyLViCFQb4wYiG2vsboP-nvkbz8qZh_MKLM7vI7AMz3ZEAJ32i6gRwwCzmuSIDUsfl4d5ke1aQ8DI",
    });
  }

  console.log(subscription);

  //await saveSubscription(subscription);
}


main();

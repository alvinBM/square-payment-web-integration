self.addEventListener("install", () => {
  self.skipWaiting();
});

self.addEventListener("push", (event) => {
  const data = event.data ? event.data.text() : {};
  event.waitUntil(self.registration.showNotification(data.title, {
    body : data.message,
    data : data
  }));
});

self.addEventListener("notificationclick", (event) => {
  const data = event.data ? event.data.text() : {};
  alert(data.url);
});

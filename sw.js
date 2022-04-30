self.addEventListener("install", () => {
  self.skipWaiting();
});

self.addEventListener("push", (event) => {
  const data = event.data ? event.data.json() : {};
  event.waitUntil(self.registration.showNotification(data.title, {
    body : data.message,
    icon : data.icon,
    data : data
  }));
  console.log(data)
});

self.addEventListener("notificationclick", (event) => {
  const data = event.notification.data;
  event.notification.close(); 
  event.waitUntil(
    openUrl(data.url)
  )
});


async function openUrl(url) {
  const windows = await self.clients.matchAll({type : 'window', includeUncontrolled : true})
  
  for (let i = 0; i < windows.length; i++) {
    const client = windows[i];
    if (client.url === url && 'focus' in client){
      return client.focus();
    }
  }

  if (self.clients.openWindow) {
    return self.clients.openWindow(url)
  }

  return null;

}

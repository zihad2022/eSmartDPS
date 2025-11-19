      function countdown(endsAt) {
          return {
              endsAt: new Date(endsAt),
              remaining: '',

              start() {
                  this.update();
                  setInterval(() => this.update(), 1000);
              },

              update() {
                  const now = new Date();
                  let diff = this.endsAt - now;

                  if (diff <= 0) {
                      this.remaining = "Expired";
                      return;
                  }

                  // Convert diff to units
                  let seconds = Math.floor(diff / 1000);
                  let minutes = Math.floor(seconds / 60);
                  let hours = Math.floor(minutes / 60);
                  let days = Math.floor(hours / 24);

                  let years = Math.floor(days / 365);
                  days -= years * 365;

                  let months = Math.floor(days / 30);
                  days -= months * 30;

                  hours = hours % 24;
                  minutes = minutes % 60;
                  seconds = seconds % 60;

                  // Build readable string
                  let parts = [];

                  if (years > 0) parts.push(years + "y");
                  if (months > 0) parts.push(months + "m");
                  if (days > 0) parts.push(days + "d");
                  if (hours > 0) parts.push(hours + "h");
                  if (minutes > 0) parts.push(minutes + "m");
                  parts.push(seconds + "s");

                  this.remaining = parts.slice(0, 4).join(" ");
              }
          }
      }
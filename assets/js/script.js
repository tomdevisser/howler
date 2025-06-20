document.addEventListener("DOMContentLoaded", function () {
  const { __ } = wp.i18n;
  const feedbackButton = document.getElementById("howler-feedback-button");
  const feedbackPopup = document.getElementById("howler-feedback-popup");
  const feedbackNotification = document.getElementById(
    "howler-feedback-notification"
  );

  if (feedbackButton && feedbackPopup) {
    feedbackButton.addEventListener("click", () => {
      feedbackPopup.hidden = !feedbackPopup.hidden;

      const canvas = document.getElementById("howler-canvas");
      const context = canvas?.getContext("2d");

      if (canvas && context && feedbackPopup.hidden === false) {
        html2canvas(document.body, {
          ignoreElements: (el) => {
            return (
              el.id === "wpadminbar" ||
              el.id === "howler-feedback-popup" ||
              el.id === "howler-feedback-button"
            );
          },
          y: window.scrollY,
          x: window.scrollX,
          width: window.innerWidth,
          height: window.innerHeight,
          scale: window.devicePixelRatio,
          useCORS: true,
        }).then((screenshotCanvas) => {
          // Preserve aspect ratio and improve rendering quality by using explicit dimensions
          const width = screenshotCanvas.width;
          const height = screenshotCanvas.height;
          canvas.width = width;
          canvas.height = height;
          context.clearRect(0, 0, width, height);
          context.drawImage(screenshotCanvas, 0, 0, width, height);
        });
      }

      let drawing = false;

      canvas.addEventListener("mousedown", (e) => {
        drawing = true;
        context.beginPath();
        context.moveTo(e.offsetX, e.offsetY);
      });

      canvas.addEventListener("mousemove", (e) => {
        if (!drawing) return;
        context.lineTo(e.offsetX, e.offsetY);
        context.stroke();
      });

      canvas.addEventListener("mouseup", () => {
        drawing = false;
      });

      canvas.addEventListener("mouseleave", () => {
        drawing = false;
      });
    });
  }

  const submitButton = document.getElementById("howler-feedback-submit-button");
  const feedbackTitleField = document.getElementById("feedback-title");
  const feedbackField = document.getElementById("feedback");

  if (submitButton && feedbackField) {
    submitButton.addEventListener("click", function () {
      submitButton.disabled = true;
      submitButton.textContent = __("Sending...", "howler");

      const feedbackTitle = feedbackTitleField.value;
      const feedback = feedbackField.value;
      const canvas = document.getElementById("howler-canvas");
      const howlerSpinner = document.getElementById("howler-spinner");
      const screenshot = canvas ? canvas.toDataURL("image/png") : "";

      if (!feedback || !feedbackTitle) return;

      howlerSpinner.hidden = false;

      fetch(howler.howler_ajax_url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "howler_send_feedback",
          feedback,
          feedback_title: feedbackTitle,
          screenshot,
        }),
      })
        .then(() => {
          submitButton.disabled = false;
          submitButton.textContent = __("'Send to Trello'", "howler");
          howlerSpinner.remove();
          feedbackPopup.hidden = true;

          if (feedbackNotification) {
            feedbackNotification.textContent = __(
              "Thank you for your feedback!",
              "howler"
            );
            feedbackNotification.style.opacity = 1;

            setTimeout(() => {
              feedbackNotification.style.opacity = 0;
            }, 3000);

            setTimeout(() => {
              feedbackNotification.textContent = "";
            }, 3100);
          }
        })
        .catch(() => {
          howlerSpinner.remove();
          alert(__("Failed to send feedback.", "howler"));
        });
    });
  }
});

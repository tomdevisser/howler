document.addEventListener("DOMContentLoaded", function () {
  const { __ } = wp.i18n;
  const feedbackButton = document.getElementById(
    "site-feedback-feedback-button"
  );
  const feedbackPopup = document.getElementById("site-feedback-feedback-popup");
  const feedbackNotification = document.getElementById(
    "site-feedback-feedback-notification"
  );

  if (feedbackButton && feedbackPopup) {
    feedbackButton.addEventListener("click", () => {
      feedbackPopup.hidden = !feedbackPopup.hidden;

      const canvas = document.getElementById("site-feedback-canvas");
      const context = canvas?.getContext("2d");

      if (canvas && context && feedbackPopup.hidden === false) {
        html2canvas(document.body, {
          ignoreElements: (el) => {
            return (
              el.id === "wpadminbar" ||
              el.id === "site-feedback-feedback-popup" ||
              el.id === "site-feedback-feedback-button"
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

      let currentColor = "#000";

      document
        .querySelectorAll(".site-feedback-pencil-button")
        .forEach((button) => {
          button.addEventListener("click", () => {
            currentColor = button.dataset.color;
            document
              .querySelectorAll(".site-feedback-pencil-button")
              .forEach((btn) => btn.classList.remove("is-active"));
            button.classList.add("is-active");
          });
        });

      let drawing = false;

      canvas.addEventListener("mousedown", (e) => {
        drawing = true;
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;

        context.strokeStyle = currentColor;
        context.lineWidth = 20;
        context.lineCap = "round";
        context.beginPath();
        context.moveTo(
          (e.clientX - rect.left) * scaleX,
          (e.clientY - rect.top) * scaleY
        );
      });

      canvas.addEventListener("mousemove", (e) => {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        context.lineTo(
          (e.clientX - rect.left) * scaleX,
          (e.clientY - rect.top) * scaleY
        );
        context.stroke();
      });

      ["mouseup", "mouseleave"].forEach((event) => {
        canvas.addEventListener(event, () => {
          drawing = false;
        });
      });
    });
  }

  const submitButton = document.getElementById(
    "site-feedback-feedback-submit-button"
  );
  const feedbackTitleField = document.getElementById("feedback-title");
  const feedbackField = document.getElementById("feedback");

  if (submitButton && feedbackField) {
    submitButton.addEventListener("click", function () {
      submitButton.disabled = true;
      submitButton.textContent = __("Sending...", "site-feedback");

      const feedbackTitle = feedbackTitleField.value;
      const feedback = feedbackField.value;
      const canvas = document.getElementById("site-feedback-canvas");
      const siteFeedbackSpinner = document.getElementById(
        "site-feedback-spinner"
      );
      const screenshot = canvas ? canvas.toDataURL("image/png") : "";

      if (!feedback || !feedbackTitle) {
        alert(
          __(
            "Please fill in both the title and feedback fields.",
            "site-feedback"
          )
        );
        submitButton.disabled = false;
        submitButton.textContent = __("Send to Trello", "site-feedback");
        return;
      }

      siteFeedbackSpinner.hidden = false;

      fetch(siteFeedback.site_feedback_ajax_url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "site_feedback_send_feedback",
          feedback,
          feedback_title: feedbackTitle,
          screenshot,
        }),
      })
        .then(() => {
          submitButton.disabled = false;
          submitButton.textContent = __("'Send to Trello'", "site-feedback");
          siteFeedbackSpinner.remove();
          feedbackPopup.hidden = true;

          if (feedbackNotification) {
            feedbackNotification.textContent = __(
              "Thank you for your feedback!",
              "site-feedback"
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
          siteFeedbackSpinner.remove();
          alert(__("Failed to send feedback.", "site-feedback"));
        });
    });
  }
});

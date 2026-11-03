
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

senderEmail = "mentorshipmatch@gmail.com"
senderPassword = "your_app_password"

subject = "MentorshipMatch: Session Booking Confirmation"

def automated_email(recipientEmail, mentorName, sessionDateTime, duration, sessionType):

    # Create a multipart message
    msg = MIMEMultipart()
    msg['From'] = senderEmail
    msg['To'] = recipientEmail
    msg['Subject'] = subject

    # Create the email body
    body = """Hello,

Your mentoring session has been successfully booked with the following details:

Mentor: {}
Date and Time: {}
Duration: {} minutes
Session Type: {}

Thank you for using MentorshipMatch!

Best regards,
MentorshipMatch Team""".format(
        mentorName,
        sessionDateTime,
        duration,
        sessionType
    )

    # Attach the email body
    msg.attach(MIMEText(body, 'plain'))

    try:
        # Connect to Gmail's SMTP server
        with smtplib.SMTP_SSL('smtp.gmail.com', 465) as server:
            server.login(senderEmail, senderPassword)
            server.send_message(msg)

            print("Email sent successfully!")

    except Exception as e:
        print(f"Error sending email: {e}")

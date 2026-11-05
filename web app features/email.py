
import os
import sys
from pathlib import Path

# This file is named email.py, so remove its directory to avoid shadowing Python's email package.
script_directory = str(Path(__file__).resolve().parent)
sys.path = [entry for entry in sys.path if str(Path(entry or os.getcwd()).resolve()) != script_directory]

import smtplib
import ssl
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

subject = "MentorshipMatch: Session Booking Confirmation"


def automated_email(recipientEmail, mentorName, sessionDateTime, duration, sessionType):
    sender_email = os.environ.get("MENTORSHIPMATCH_SMTP_USERNAME")
    sender_password = os.environ.get("MENTORSHIPMATCH_SMTP_PASSWORD")
    if not sender_email or not sender_password:
        raise ValueError("Set MENTORSHIPMATCH_SMTP_USERNAME and MENTORSHIPMATCH_SMTP_PASSWORD.")

    msg = MIMEMultipart()
    msg['From'] = sender_email
    msg['To'] = recipientEmail
    msg['Subject'] = subject

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

    msg.attach(MIMEText(body, 'plain'))

    with smtplib.SMTP_SSL('smtp.gmail.com', 465, context=ssl.create_default_context(), timeout=20) as server:
        server.login(sender_email, sender_password)
        server.send_message(msg)


if __name__ == '__main__':
    if len(sys.argv) != 6:
        print('Expected recipient, mentor, date/time, duration, and session type.', file=sys.stderr)
        sys.exit(2)
    try:
        automated_email(sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4], sys.argv[5])
    except (OSError, smtplib.SMTPException, ValueError) as error:
        print(f'Unable to send booking email: {error}', file=sys.stderr)
        sys.exit(1)


import os
import sys
from pathlib import Path

# Prevent email.py from shadowing Python's email package.
script_directory = str(Path(__file__).resolve().parent)
sys.path = [
    entry for entry in sys.path
    if str(Path(entry or os.getcwd()).resolve()) != script_directory
]

import smtplib
import ssl
from datetime import datetime
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

subject = "MentorshipMatch: Session Booking Confirmation"


def automated_email(
    recipientEmail,
    mentorName,
    sessionDateTime,
    duration,
    sessionType,
    mentorCareerField="Not specified"
):

    sender_email = os.getenv("MENTORSHIPMATCH_SMTP_USERNAME")
    sender_password = os.getenv("MENTORSHIPMATCH_SMTP_PASSWORD")
    smtp_host = os.getenv("MENTORSHIPMATCH_SMTP_HOST", "smtp.gmail.com")
    smtp_security = os.getenv("MENTORSHIPMATCH_SMTP_SECURITY", "ssl").lower()

    if not sender_email or not sender_password:
        raise ValueError(
            "Set MENTORSHIPMATCH_SMTP_USERNAME and "
            "MENTORSHIPMATCH_SMTP_PASSWORD in the server environment."
        )
    if smtp_security not in ("ssl", "starttls"):
        raise ValueError("MENTORSHIPMATCH_SMTP_SECURITY must be ssl or starttls.")

    # datetime-local submits a local time without a timezone; format it without conversion.
    formatted_session_datetime = datetime.fromisoformat(sessionDateTime).strftime(
        "%B %d, %Y at %I:%M %p"
    )
    # Keep older callers working when they don't provide the mentor's field.
    mentorCareerField = mentorCareerField.strip() or "Not specified"
    default_port = "465" if smtp_security == "ssl" else "587"
    smtp_port = int(os.getenv("MENTORSHIPMATCH_SMTP_PORT", default_port))

    # Create email
    msg = MIMEMultipart()
    msg['From'] = sender_email
    msg['To'] = recipientEmail
    msg['Subject'] = subject

    body = """Hello,

Your mentoring session has been successfully booked with the following details:

Mentor: {}
Career Field: {}
Date and Time: {}
Duration: {} minutes
Session Type: {}

Thank you for using MentorshipMatch! We hope you have an awesome and educational session with your mentor.

Best regards,
MentorshipMatch Team""".format(
        mentorName,
        mentorCareerField,
        formatted_session_datetime,
        duration,
        sessionType
    )

    msg.attach(MIMEText(body, 'plain'))

    ssl_context = ssl.create_default_context()
    # Hosting providers may require implicit SSL or STARTTLS.
    if smtp_security == "ssl":
        connection = smtplib.SMTP_SSL(
            smtp_host,
            smtp_port,
            context=ssl_context,
            timeout=20
        )
    else:
        connection = smtplib.SMTP(smtp_host, smtp_port, timeout=20)

    with connection as server:
        if smtp_security == "starttls":
            server.starttls(context=ssl_context)

        print(f"Connected to SMTP server {smtp_host}.")
        print("Authenticating...")

        server.login(sender_email, sender_password)

        print("Authentication successful!")
        print("Sending email...")

        server.send_message(msg)

        print("Email accepted by the SMTP server.")


if __name__ == '__main__':

    # PHP supplies the recipient, booking details, and career field as arguments.
    if len(sys.argv) != 7:
        print(
            'Expected recipient, mentor, date/time, duration, session type, and career field.',
            file=sys.stderr
        )
        sys.exit(2)

    try:
        automated_email(
            sys.argv[1],
            sys.argv[2],
            sys.argv[3],
            sys.argv[4],
            sys.argv[5],
            sys.argv[6]
        )

    except (OSError, smtplib.SMTPException, ValueError) as error:
        print(f'Unable to send booking email: {error}', file=sys.stderr)
        sys.exit(1)

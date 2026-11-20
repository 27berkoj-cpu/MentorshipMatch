
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


def send_email(msg):
    # Read SMTP credentials at send time so secrets stay in the server environment.
    sender_email = os.getenv("MENTORSHIPMATCH_SMTP_USERNAME")
    sender_password = os.getenv("MENTORSHIPMATCH_SMTP_PASSWORD")
    smtp_host = os.getenv("MENTORSHIPMATCH_SMTP_HOST", "smtp.gmail.com")
    smtp_security = os.getenv("MENTORSHIPMATCH_SMTP_SECURITY", "ssl").lower()

    if not sender_email or not sender_password:
        raise ValueError(
            "Set MENTORSHIPMATCH_SMTP_USERNAME and "
            "MENTORSHIPMATCH_SMTP_PASSWORD in the server environment."
        )
    msg['From'] = sender_email
    if smtp_security not in ("ssl", "starttls"):
        raise ValueError("MENTORSHIPMATCH_SMTP_SECURITY must be ssl or starttls.")

    default_port = "465" if smtp_security == "ssl" else "587"
    smtp_port = int(os.getenv("MENTORSHIPMATCH_SMTP_PORT", default_port))
    ssl_context = ssl.create_default_context()

    # Support servers configured for implicit TLS or STARTTLS.
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

        server.login(sender_email, sender_password)
        server.send_message(msg)


def automated_email(
    recipientEmail,
    mentorName,
    sessionDateTime,
    duration,
    sessionType,
    mentorCurrentCareer="Not specified",
    mentorField="Not specified",
):
    # datetime-local submits a local time without a timezone; format it without conversion.
    formatted_session_datetime = datetime.fromisoformat(sessionDateTime).strftime(
        "%B %d, %Y at %I:%M %p"
    )
    mentorCurrentCareer = mentorCurrentCareer.strip() or "Not specified"
    mentorField = mentorField.strip() or "Not specified"

    # Create email
    msg = MIMEMultipart()
    msg['To'] = recipientEmail
    msg['Subject'] = subject

    body = """Hello,

Your mentoring session has been successfully booked with the following details:

Mentor: {}
Current Career: {}
Field: {}
Date and Time: {}
Duration: {} minutes
Session Type: {}

Thank you for using MentorshipMatch! We hope you have an awesome and educational session with your mentor.

Best regards,
MentorshipMatch Team""".format(
        mentorName,
        mentorCurrentCareer,
        mentorField,
        formatted_session_datetime,
        duration,
        sessionType
    )

    msg.attach(MIMEText(body, 'plain'))
    send_email(msg)


def mentor_booking_notification(
    recipientEmail,
    mentorName,
    studentName,
    studentEmail,
    sessionDateTime,
    duration,
    sessionType,
    requestsUrl,
):
    # The mentor should know the request is pending and have a direct way to respond.
    formatted_session_datetime = datetime.fromisoformat(sessionDateTime).strftime(
        "%B %d, %Y at %I:%M %p"
    )

    msg = MIMEMultipart()
    msg['To'] = recipientEmail
    msg['Subject'] = "MentorshipMatch: A Student Has Requested a Session"

    body = """Hello, {},

A student has requested a mentoring session with you. The request is pending your response.

Student: {}
Student Email: {}
Date and Time: {}
Duration: {} minutes
Session Type: {}

Review and respond to the request: {}

Best regards,
MentorshipMatch Team""".format(
        mentorName,
        studentName,
        studentEmail,
        formatted_session_datetime,
        duration,
        sessionType,
        requestsUrl
    )

    msg.attach(MIMEText(body, 'plain'))
    send_email(msg)


if __name__ == '__main__':

    # The flag selects the mentor notification; the original argument list remains
    # available for student confirmations.
    if len(sys.argv) == 10 and sys.argv[1] == '--mentor-notification':
        try:
            mentor_booking_notification(
                sys.argv[2],
                sys.argv[3],
                sys.argv[4],
                sys.argv[5],
                sys.argv[6],
                sys.argv[7],
                sys.argv[8],
                sys.argv[9],
            )
        except (OSError, smtplib.SMTPException, ValueError) as error:
            print(f'Unable to send mentor booking notification: {error}', file=sys.stderr)
            sys.exit(1)
    elif len(sys.argv) == 8:
        # PHP supplies the student recipient, mentor, booking details, career, and field.
        try:
            automated_email(
                sys.argv[1],
                sys.argv[2],
                sys.argv[3],
                sys.argv[4],
                sys.argv[5],
                sys.argv[6],
                sys.argv[7],
            )
        except (OSError, smtplib.SMTPException, ValueError) as error:
            print(f'Unable to send booking email: {error}', file=sys.stderr)
            sys.exit(1)
    else:
        print(
            'Expected student booking details or mentor notification details.',
            file=sys.stderr
        )
        sys.exit(2)

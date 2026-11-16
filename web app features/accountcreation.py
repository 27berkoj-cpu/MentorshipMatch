import os
import sys
from pathlib import Path
import smtplib
import ssl
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

# Prevent email.py from shadowing Python's email package.
script_directory = str(Path(__file__).resolve().parent)
sys.path = [
    entry for entry in sys.path
    if str(Path(entry or os.getcwd()).resolve()) != script_directory
]

subject = "Hello from MentorshipMatch: Account Creation Confirmation"


def automated_email(recipientEmail, userName):
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

    default_port = "465" if smtp_security == "ssl" else "587"
    smtp_port = int(os.getenv("MENTORSHIPMATCH_SMTP_PORT", default_port))

    msg = MIMEMultipart()
    msg['From'] = sender_email
    msg['To'] = recipientEmail
    msg['Subject'] = subject

    body = f"""Hello {userName},

    Welcome to MentorshipMatch! Your account is now active, and you can start exploring the many features we offer.

    Thank you for joining our community of mentors and students. We are excited to have you on board and look forward to helping you achieve your goals.

    Best regards,
    The MentorshipMatch Team
    """

    msg.attach(MIMEText(body, 'plain')) # Attach the body of the email as plain text
    ssl_context = ssl.create_default_context()
    if smtp_security == "ssl":
        connection = smtplib.SMTP_SSL(
            smtp_host,
            smtp_port,
            context=ssl_context,
            timeout=20,
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
        print("Sending account creation email...")
        server.send_message(msg)
        print("Account creation email accepted by the SMTP server.")

if __name__ == '__main__':
    if len(sys.argv) != 3:
        print(
            'Expected recipient email and username.',
            file=sys.stderr,
        )
        sys.exit(2)

    try:
        automated_email(sys.argv[1], sys.argv[2])
    except (OSError, smtplib.SMTPException, ValueError) as error:
        print(f'Unable to send account creation email: {error}', file=sys.stderr)
        sys.exit(1)



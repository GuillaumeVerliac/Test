const express = require('express');
const nodemailer = require('nodemailer');
const path = require('path');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());
app.use(express.static(path.join(__dirname)));

const transporter = nodemailer.createTransport({
  host: process.env.OVH_SMTP_HOST,
  port: Number(process.env.OVH_SMTP_PORT),
  secure: process.env.OVH_SMTP_SECURE === 'true',
  auth: {
    user: process.env.OVH_SMTP_USER,
    pass: process.env.OVH_SMTP_PASS
  }
});

app.post('/api/newsletter', async (req, res) => {
  try {
    const { firstname, lastname, company, email } = req.body || {};

    if (!firstname || !lastname || !email) {
      return res.status(400).json({
        message: 'Prénom, nom et e-mail sont obligatoires.'
      });
    }

    const cleanFirstname = String(firstname).trim();
    const cleanLastname = String(lastname).trim();
    const cleanCompany = String(company || '').trim();
    const cleanEmail = String(email).trim();

    const subject = `${cleanFirstname} ${cleanLastname} souhaite s'inscrire à la newsletter`;

    const text = [
      `${cleanFirstname} ${cleanLastname} souhaite s'inscrire à la newsletter.`,
      '',
      `Prénom : ${cleanFirstname}`,
      `Nom : ${cleanLastname}`,
      `Entreprise : ${cleanCompany || 'Non renseignée'}`,
      `E-mail : ${cleanEmail}`
    ].join('\n');

    await transporter.sendMail({
      from: `"Site Priam Solutions" <${process.env.OVH_SMTP_USER}>`,
      to: process.env.NEWSLETTER_TO_EMAIL,
      replyTo: cleanEmail,
      subject,
      text
    });

    return res.status(200).json({
      message: 'E-mail envoyé.'
    });
  } catch (error) {
    console.error('Erreur envoi newsletter :', error);
    return res.status(500).json({
      message: "Impossible d'envoyer l'e-mail."
    });
  }
});

app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});

app.listen(PORT, () => {
  console.log(`Serveur lancé sur http://localhost:${PORT}`);
});

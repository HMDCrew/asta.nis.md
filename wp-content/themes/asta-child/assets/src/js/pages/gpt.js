const fs = require('fs');
const openai = require('openai');

// Leggi il contenuto del file di testo
const fileContent = fs.readFileSync('percorso/del/file.txt', 'utf-8');

console.log( fileContent )
// // Domanda da porre al chatbot
// const userQuestion = "Mi descrivi il personaggio di Richard?";

// // Combinazione di domanda e contenuto del file di testo come prompt
// const prompt = `${userQuestion}\n${fileContent}`;

// // Imposta la tua chiave API di OpenAI
// const apiKey = 'INSERISCI_LA_TUA_CHIAVE_API';
// openai.apiKey = apiKey;

// // Effettua la richiesta all'API di OpenAI
// const response = await openai.Completion.create({
//   engine: 'text-davinci-003',
//   prompt: prompt,
//   max_tokens: 100,
// });

// // Ottieni la risposta dalla risposta dell'API di OpenAI
// const answer = response.choices[0].text.trim();
// console.log(answer);
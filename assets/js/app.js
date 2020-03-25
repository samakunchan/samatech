/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

// Les images qui sont dans l'import (ex: style.css) n'ont pas besoin de ces lignes ci dessous.
// Elles sont utiles uniquement pour les fichiers statiques vanilla.
const imagesContext = require.context("../media", true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);
// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';


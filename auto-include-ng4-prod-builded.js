'use strict';

const fs = require('fs');
const path = require('path');
const cheerio = require('cheerio');

const keywords = ['inline', 'main', 'polyfills', 'scripts', 'styles', 'vendor'];

const FirstApp = JSON.parse(fs.readFileSync(path.join(__dirname, './.angular-cli.json'))).apps[0];

try {

    // Read and get all filename deployed.
    const deployedDir = fs.readdirSync(FirstApp.outDir);

    // Keep new filename in variable
    const newFilename = deployedDir.filter(function (filename) {
        return keywords.some(function (keyword) { return (filename.indexOf(keyword) > -1) && (filename.slice(-3) !== 'map') });
    });

    // Map new filename with keywords
    const mappedNewFilenameKeywords = keywords.map(function(keyword, index) {
        return { keyword: keyword, filename: newFilename[index] };
    });

    // Read the blade file that we aim to include to.
    const indexHtml = fs.readFileSync(FirstApp.viewInclude).toString();
    const distPath = FirstApp.outDir.replace('public/', '') + '/';

    const $ = cheerio.load(indexHtml);

    var scriptsElement = $('script').toArray();
    var stylesElement = $('link').toArray();

    // Find the point included
    mappedNewFilenameKeywords.forEach(function (obj) {

        var filePath = distPath + obj.filename;

        if (isJsFile(obj.filename)) { // In case JS file

            var targetElement = scriptsElement.find(function (script) { return script.attribs.src.indexOf(obj.keyword) > -1; });

            if (targetElement) {
                // Replace when found; otherwise create new one
                $('script').filter(targetElement).attr('src', filePath);
            } else {
                $('body').append("<script type=\"text/javascript\" src=\"" + filePath +"\"></script>");

                // Remove old include styles bundle if there is.
                if (stylesElement.find(function (style) { return style.attribs.href.indexOf(obj.keyword) > -1; })) {
                    var targetRemove = stylesElement.find(function (style) { return style.attribs.href.indexOf(obj.keyword) > -1; });
                    $('link').filter(targetRemove).remove();
                }
            }
        } else { // In case css file as styles in prod build

            var targetElement = stylesElement.find(function (style) { return style.attribs.href.indexOf(obj.keyword) > -1; });

            if (targetElement) {
                // Replace when found; otherwise create new one
                $('style').filter(targetElement).attr('href', filePath);
            } else {
                $('head').append("<link rel=\"stylesheet\" href=\"" + filePath + "\" >");

                // Remove old include script bundle if there is.
                if (scriptsElement.find(function (script) { return script.attribs.src.indexOf(obj.keyword) > -1; })) {
                    var targetRemove = scriptsElement.find(function (script) { return script.attribs.src.indexOf(obj.keyword) > -1; });
                    $('script').filter(targetRemove).remove();
                }
            }
        }

    });

    fs.writeFileSync(FirstApp.viewInclude, $.html());

    console.log(FirstApp.viewInclude + 'has been included the angular file bundle.');

} catch (err) {

    console.error(err.message);

}

// Check there is JS file if not there is CSS file
function isJsFile(filename) {
    return filename.slice(-2) === 'js';
}
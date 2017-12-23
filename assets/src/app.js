const $ = require('jquery');
async function foo () {
    try {
        let bar = await $.post();
        console.log(bar);
    } catch (err) {
        console.log(err);
    }
}
foo();
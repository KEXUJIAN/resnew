import $ from 'jquery';
async function foo () {
    let bar = await $.post();
    console.log(bar);
}
foo();
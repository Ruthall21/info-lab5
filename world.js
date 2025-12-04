"use strict";

window.onload = () => {
    const lookupBtn = document.getElementById("lookup");
    const lookupCitiesBtn = document.getElementById("lookup-cities");
    const input = document.getElementById("country");
    const resultsDiv = document.getElementById("result");

    // COUNTRY LOOKUP
    lookupBtn.addEventListener("click", () => {
        const country = input.value.trim();
        fetch(`world.php?country=${country}`)
            .then(response => response.text())
            .then(data => {
                resultsDiv.innerHTML = data;
            })
            .catch(err => console.error(err));
    });

    // CITIES LOOKUP
    lookupCitiesBtn.addEventListener("click", () => {
        const country = input.value.trim();
        fetch(`world.php?country=${country}&lookup=cities`)
            .then(response => response.text())
            .then(data => {
                resultsDiv.innerHTML = data;
            })
            .catch(err => console.error(err));
    });
};

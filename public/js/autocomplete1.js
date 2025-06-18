// const autoCompleteJS = new autoComplete({
//     selector: "#autoCompletetwo",
//     placeHolder: "Search for Food...",
//     data: {
//         src: ["Sauce - Thousand Island", "Wild Boar - Tenderloin", "Goat - Whole Cut"],
//         cache: true,
//     },
//     resultsList: {
//         element: (list, data) => {
//             if (!data.results.length) {
//                 // Create "No Results" message element
//                 const message = document.createElement("div");
//                 // Add class to the created element
//                 message.setAttribute("class", "no_result");
//                 // Add message text content
//                 message.innerHTML = `<span>Found No Results for "${data.query}"</span>`;
//                 // Append message element to the results list
//                 list.prepend(message);
//             }
//         },
//         noResults: true,
//     },
//     resultItem: {
//         element: (item, data) => {
//             debugger
//             // Modify Results Item Style
//             item.style = "display: flex;text-transform: capitalize;align-items: center;";
//             // Modify Results Item Content
//             item.innerHTML = `<svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
// <rect y="0.5" width="32" height="32" rx="16" fill="#F7F7F7"/>
// <g clip-path="url(#clip0_1137_4833)">
// <path d="M16.0973 9.22852C19.0061 9.22852 21.43 11.6525 21.43 14.5612C21.43 16.7912 18.2304 21.1543 16.776 22.9965C16.5821 23.1904 16.3882 23.3844 16.0973 23.3844C15.8065 23.3844 15.6125 23.2874 15.4186 22.9965C13.9643 21.1543 10.7646 16.7912 10.7646 14.5612C10.7646 11.6525 13.1886 9.22852 16.0973 9.22852Z" stroke="#717171" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
// <path d="M16.0971 15.9286C16.3081 15.9286 16.5171 15.887 16.712 15.8063C16.907 15.7255 17.0841 15.6072 17.2334 15.458C17.3826 15.3087 17.5009 15.1316 17.5817 14.9367C17.6624 14.7417 17.704 14.5327 17.704 14.3217C17.704 14.1107 17.6624 13.9018 17.5817 13.7068C17.5009 13.5118 17.3826 13.3347 17.2334 13.1855C17.0841 13.0363 16.907 12.9179 16.712 12.8372C16.5171 12.7564 16.3081 12.7148 16.0971 12.7148C15.6711 12.7148 15.2625 12.8841 14.9612 13.1853C14.66 13.4866 14.4907 13.8952 14.4907 14.3212C14.4907 14.7473 14.66 15.1559 14.9612 15.4571C15.2625 15.7584 15.6711 15.9286 16.0971 15.9286Z" stroke="#717171" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
// </g>
// <defs>
// <clipPath id="clip0_1137_4833">
// <rect width="13.0893" height="15.998" fill="white" transform="translate(9.45557 8.5)"/>
// </clipPath>
// </defs>
// </svg>
// <div style="margin-left: 16px;"><span style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;color: #B0B0B0;font-size: 14px;font-weight: 400;">${data.match}
// </span>
// <span style="display: flex; align-items: center;color: #B0B0B0;font-size: 12px;font-weight: 400;">
// ${data.key}
// </span>  </div>`;
//         },
//         highlight: true
//     },
//     events: {
//         input: {
//             selection: (event) => {
//                 const selection = event.detail.selection.value;
//                 autoCompleteJS.input.value = selection;


//                 var testObject = autoCompleteJS.input.value;

//                 localStorage.setItem('testObject', JSON.stringify(testObject));
//                 var retrievedObject = localStorage.getItem('testObject');

//                 console.log(JSON.parse(retrievedObject));
//             },
//             focus: (event) => {
//                 debugger

//             },
//             click: (event,s) => {
//             }

//         },
//     }

// });


// document.querySelector("#autoComplete1").addEventListener("click", function (event) {
//     // "event.detail" carries the returned data values
//     debugger
//     console.log(event);
// });


const data = [
    { city: "Apache Junction", country: "Maricopa, Pinal" },
    { city: "London", country: "England" },
    { city: "Avondale", country: "Maricopa" },
    { city: "Bullhead", country: "Mohave" },
    { city: "Casa Grande", country: "Pinal" },
    { city: "Flagstaff city", country: "Coconino" },
    { city: "Gilbert town", country: "Maricopa" },
    { city: "Glendale city", country: "Maricopa" },
    { city: "Lake Havasu City", country: "Mohave" },
    { city: "Mesa city", country: "Maricopa" },
];

const autocomplete = document.getElementById("autocompleteFive");
const resultsHTML = document.getElementById("results");


autocomplete.oninput = function () {
    let results = [];
    const userInput = this.value;
    resultsHTML.innerHTML = "";
    if (userInput.length > 0) {
        results = getResults(userInput);
        displayList(results)
    }
};

autocomplete.onclick = function () {
    let results = [];
    if (!resultsHTML.hasChildNodes()) {
        let retrievedObject = localStorage.getItem('testObject');
        results = JSON.parse(retrievedObject);
        // results = getResults(userInput);
        if (results != null) {
            displayList(results)
        }
    }
};

function displayList(results) {
    resultsHTML.style.display = "block";
    for (i = 0; i < results.length; i++) {
        resultsHTML.innerHTML += "<li>" + `<svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
     <rect y="0.5" width="32" height="32" rx="16" fill="#F7F7F7"/>
     <g clip-path="url(#clip0_1137_4833)">
     <path d="M16.0973 9.22852C19.0061 9.22852 21.43 11.6525 21.43 14.5612C21.43 16.7912 18.2304 21.1543 16.776 22.9965C16.5821 23.1904 16.3882 23.3844 16.0973 23.3844C15.8065 23.3844 15.6125 23.2874 15.4186 22.9965C13.9643 21.1543 10.7646 16.7912 10.7646 14.5612C10.7646 11.6525 13.1886 9.22852 16.0973 9.22852Z" stroke="#717171" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
     <path d="M16.0971 15.9286C16.3081 15.9286 16.5171 15.887 16.712 15.8063C16.907 15.7255 17.0841 15.6072 17.2334 15.458C17.3826 15.3087 17.5009 15.1316 17.5817 14.9367C17.6624 14.7417 17.704 14.5327 17.704 14.3217C17.704 14.1107 17.6624 13.9018 17.5817 13.7068C17.5009 13.5118 17.3826 13.3347 17.2334 13.1855C17.0841 13.0363 16.907 12.9179 16.712 12.8372C16.5171 12.7564 16.3081 12.7148 16.0971 12.7148C15.6711 12.7148 15.2625 12.8841 14.9612 13.1853C14.66 13.4866 14.4907 13.8952 14.4907 14.3212C14.4907 14.7473 14.66 15.1559 14.9612 15.4571C15.2625 15.7584 15.6711 15.9286 16.0971 15.9286Z" stroke="#717171" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
     </g>
     <defs>
     <clipPath id="clip0_1137_4833">
     <rect width="13.0893" height="15.998" fill="white" transform="translate(9.45557 8.5)"/>
     </clipPath>
     </defs>
     </svg>
     <div style="margin-left: 16px;"><span id='li-value' style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;color: #B0B0B0;font-size: 14px;font-weight: 400;">${results[i].city}
     </span> 
     <span style="display: flex; align-items: center;color: #B0B0B0;font-size: 12px;font-weight: 400;">
     ${results[i].country}
     </span> </div>` + "</li>";
    }
}

function getResults(input) {
    const dataFilter = data.filter(m => m.city.toLowerCase().includes(input.toLowerCase()));
    let retrievedObject = localStorage.getItem('testObject');
    retrievArray = JSON.parse(retrievedObject);

    if (retrievArray != null) {
        retrievArray.filter(m => m.city.toLowerCase().includes(input.toLowerCase()));
    }
    else {
        retrievArray = [];
    }

    let results = [...new Set([...retrievArray, ...dataFilter])];
    return results;
}

resultsHTML.onclick = function (event) {
    
    const city = event.target.innerText.split("\n")[0];
    let item = data.find(m => m.city == city);
    if (item) {
        autocomplete.value = city + ' - ' + item.country;
        let retrievedObject = localStorage.getItem('testObject');
        let array = JSON.parse(retrievedObject);
        if (array == null) array = [item];
        if (!array.some(m => m.city == city)) {
            array.push(item)
        }
        localStorage.setItem('testObject', JSON.stringify(array));

        this.innerHTML = "";
    }

};
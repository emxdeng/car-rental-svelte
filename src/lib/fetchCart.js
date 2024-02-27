async function fetchCarData() {
    try {
        let response = await fetch('/cars.json');
        let json = await response.json();
        return json.cars;
    } catch (error) {
        console.error('Error fetching car data:', error);
    }
}

export {fetchCarData};

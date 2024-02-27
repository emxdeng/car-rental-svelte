<script>
	import { onMount, afterUpdate } from 'svelte';
	import { fetchCarData } from '$lib/fetchCart';
	import { identity } from 'svelte/internal';
	/**
	 * @type {any[]}
	 */
	let cart = [];

	/**
	 * @type {any[]}
	 */
	let cars = [];

	let firstName = '';
	let lastName = '';
	let email = '';
	let addressLine1 = '';
	let addressLine2 = '';
	let city = '';
	let state = '';
	/**
	 * @type {number}
	 */
	let postcode;
	let paymentType = '';

	let bond = 0;
	let bookingProcessed = false;

	let isFirstNameValid = false;
	let isLastNameValid = false;
	let isEmailValid = false;
	let isAddressLineValid = false;
	let isCityValid = false;
	let isPostCodeValid = false;

	function validateFirstName() {
		isFirstNameValid = firstName.length > 0;
	}

	function validateLastName() {
		isLastNameValid = lastName.length > 0;
	}

	function validateEmail() {
		// Basic email validation regex pattern
		const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		isEmailValid = emailPattern.test(email);
	}

	function validateAddressLine() {
		isAddressLineValid = addressLine1.length > 0;
	}

	function validateCity() {
		isCityValid = city.trim() !== '';
	}

	function validatePostCode() {
		isPostCodeValid = !isNaN(postcode) && String(postcode).length === 4;
	}

	async function fetchCart() {
		try {
			let response = await fetch(
				'http://localhost/a2/svelte-projects/car-rental-php/fetch-cart.php',
				{
					credentials: 'include'
				}
			);
			let json = await response.json();
			cart = json;
			console.log(cart);
		} catch (error) {
			console.error('Error fetching cart data:', error);
		}
	}

	/**
	 * @param {String} email
	 */
	async function calculateBond(email) {
		try {
			let response = await fetch(
				`http://localhost/a2/svelte-projects/car-rental-php/calculate-bond.php?email=${email}`,
				{
					credentials: 'include'
				}
			);
			let json = await response.json();
			bond = json;
			console.log(bond);
		} catch (error) {
			console.error('Error calculating bond:', error);
		}
	}

	async function processBooking() {
		if (
			isFirstNameValid === false ||
			isLastNameValid === false ||
			isEmailValid === false ||
			isAddressLineValid === false ||
			isCityValid === false ||
			isPostCodeValid === false
		) {
			alert('Please correct missing or invalid fields.');
			return;
		}

		//update renting history table in sql
		try {
			let response = await fetch(
				'http://localhost/a2/svelte-projects/car-rental-php/process-booking.php',
				{
					credentials: 'include',
					method: 'POST',
					// headers: {
					// 	'Content-Type': 'application/json'
					// },
					body: JSON.stringify({
						email
					})
				}
			);
			let bookingProcessed = await response.json();
			console.log(bookingProcessed);
		} catch (error) {
			console.error('Error processing booking data:', error);
		}

		//update availability in json file
		try {
			let response = await fetch(
				'http://localhost/a2/svelte-projects/car-rental-php/update-availability.php',
				{
					credentials: 'include',
					method: 'GET'
					// headers: {
					// 	'Content-Type': 'application/json'
					// // },
					// body: JSON.stringify({
					// 	email
				}
			);
		} catch (error) {
			console.error('Error updating cars.json:', error);
		}

		//clear the cart
		try {
			let response = await fetch(
				'http://localhost/a2/svelte-projects/car-rental-php/clear-cart.php',
				{
					credentials: 'include',
					method: 'GET'
					// headers: {
					// 	'Content-Type': 'application/json'
					// // },
					// body: JSON.stringify({
					// 	email
				}
			);
		} catch (error) {
			console.error('Error clearing cart:', error);
		}

		//redirect to confirmation page
		window.location.href = '/confirmation';
	}

	onMount(async () => {
		cars = await fetchCarData();
		await fetchCart();
	});

	afterUpdate(() => {
		if (bookingProcessed) {
			window.location.href = '/confirmation';
		}
	});

	/**
	 * @type {number}
	 */
	let totalCost;

	//recalculate total cost when any of the variables change
	$: {
		let total = 0;
		cart.forEach((item) => {
			total = total + cars.find((car) => car.id == item.id).price_per_day * item.rental_days;
		});
		totalCost = total + bond;
	}

	//recalculate the bond whenever email is updated
	$: {
		calculateBond(email);
	}
</script>

<svelte:head>
	<title>Car Reservations</title>
	<meta name="description" content="List of car reservations" />
</svelte:head>

<body>
	{#if !bookingProcessed}
		<div class="container">
			<div class="left">
				<h1>Order Placement</h1>
				<center>
					All fields are required to complete booking.
					<br /><br />
					Please note: There is a $200 bond if you have not rented from us in the past 3 months
					<br />
					<form>
						<table>
							<tr>
								<td><label for="first-name">First Name:</label></td>
								<td
									><input
										type="text"
										id="first-name"
										name="first-name"
										bind:value={firstName}
										on:input={validateFirstName}
										required
									/>
								</td>
								{#if !isFirstNameValid}
									<p class="error">Required field is empty</p>
								{/if}
							</tr>
							<tr>
								<td><label for="last-name">Last Name:</label></td>
								<td
									><input
										type="text"
										id="last-name"
										name="last-name"
										bind:value={lastName}
										on:input={validateLastName}
										required
									/>
								</td>{#if !isLastNameValid}
									<p class="error">Required field is empty</p>
								{/if}
							</tr>
							<tr>
								<td><label for="email">Email Address:</label></td>
								<td
									><input
										type="email"
										id="email"
										name="email"
										bind:value={email}
										required
										on:input={validateEmail}
									/>
								</td>
								{#if !isEmailValid}
									<p class="error">Please enter a valid email address</p>
								{/if}
							</tr>
							<tr>
								<td>
									<label for="delivery-address">Address Line 1:</label>
								</td>
								<td
									><input
										type="text"
										id="delivery-address"
										name="delivery-address"
										bind:value={addressLine1}
										on:input={validateAddressLine}
										required
									/>
								</td>
								{#if !isAddressLineValid}
									<p class="error">Please enter a valid address</p>
								{/if}
							</tr>
							<tr>
								<td>
									<label for="delivery-address">Address Line 2:</label>
								</td>
								<td
									><input
										type="text"
										id="delivery-address"
										name="delivery-address"
										bind:value={addressLine2}
									/></td
								>
							</tr>
							<tr>
								<td><label for="suburb">City:</label></td>
								<td
									><input
										type="text"
										id="suburb"
										name="suburb"
										required
										bind:value={city}
										on:input={validateCity}
									/>
								</td>
								{#if !isCityValid}
									<p class="error">Please enter a valid city</p>
								{/if}
							</tr>
							<tr>
								<td><label for="state">State:</label></td>
								<td>
									<div class="select-container">
										<select required bind:value={state} class="payment-type">
											<option value="act" selected>ACT</option>
											<option value="nsw">NSW</option>
											<option value="qld">QLD</option>
											<option value="sa">SA</option>
											<option value="tas">TAS</option>
											<option value="nt">Northern Territory</option>
											<option value="wa">WA</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td><label for="country">Post Code:</label></td>
								<td
									><input
										type="number"
										id="country"
										name="country"
										bind:value={postcode}
										required
										on:input={validatePostCode}
									/>
								</td>
								{#if !isPostCodeValid}
									<p class="error">Please enter a valid post code</p>
								{/if}
							</tr>
							<tr>
								<td><label for="country">Payment Type:</label></td>
								<td>
									<div class="select-container">
										<select required bind:value={paymentType} class="payment-type">
											<option value="visa" selected>Visa</option>
											<option value="mastercard">Mastercard</option>
											<option value="cash">Cash</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<input
										type="submit"
										id="place-order"
										value="Complete Booking"
										on:click={() => processBooking()}
									/>
								</td>
							</tr>
						</table>
					</form>
					<!-- <input
						type="submit"
						id="place-order"
						value="Complete Booking"
						on:click={() => processBooking()}
					/> -->
				</center>
			</div>
			<div class="right">
				<h1>Order Summary</h1>
				<table>
					<tbody>
						<tr class="headings">
							<td> Item </td>
							<td> Price Per Day </td>
							<td> Rental Days </td>
						</tr>
						{#each cart as item (item.id)}
							<tr>
								<td>
									{cars.find((car) => car.id == item.id).brand}
									{cars.find((car) => car.id == item.id).model}
									{cars.find((car) => car.id == item.id).year}
									<br /><br />
									<img src={cars.find((car) => car.id == item.id).img} alt="Car" class="tableImg" />
								</td>
								<td>
									{cars.find((car) => car.id == item.id).price_per_day}
								</td>
								<td>
									{item.rental_days}
								</td>
							</tr>
						{/each}
						<tr>
							<td><hr /></td>
						</tr>
						<tr class="totalPrice">
							<td> Bond: </td>
							<td />
							<td> {bond} </td>
						</tr>
						<tr class="totalPrice">
							<td> Total Price: </td>
							<td />
							<td>{totalCost} </td>
						</tr>
						<tr>
							<td>
								<a href="/cart">
									<button>Continue Selection</button>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	{:else if bookingProcessed}
		Insert some order summary stuff here.
	{/if}
</body>

<style>
	table,
	td {
		padding: 10px;
	}

	.left {
		width: 50%;
		text-align: left;
	}

	.right {
		margin-left: 100px;
		width: 50%;
		text-align: left;
	}

	.container {
		display: flex;
		justify-content: space-between;
		margin-top: 50px !important;
		margin: auto;
		width: 90%;
		max-width: 1500px;
		background-color: rgba(255, 255, 255, 0.5);
		padding: 20px;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
	}

	table,
	td {
		padding: 10px;
	}

	.tableImg {
		width: 100px;
	}

	.headings {
		background-color: gray;
		color: #f0f4f7;
	}

	.error {
		display: block;
		margin-top: 0;
		color: red;
		font-size: 8px;
	}

	.payment-type {
		width: 100%;
	}

	.select-container {
		width: 100%;
	}
</style>

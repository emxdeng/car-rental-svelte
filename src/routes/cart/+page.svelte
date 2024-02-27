<script>
	import { onMount } from 'svelte';
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

	/**
	 * @param {number} id
	 * @param {number} days
	 */
	async function updateRentalDays(id, days) {
		try {
			let response = await fetch(
				`http://localhost/a2/svelte-projects/car-rental-php/update-cart-item.php?id=${id}&days=${days}`,
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

	function validateCheckout() {
		if (cart.length == 0) {
			alert('You have no items in your cart.');
			window.location.href = '/';
		} else {
			window.location.href = '/checkout';
		}
	}

	/**
	 * @param {number} id
	 */
	async function deleteItem(id) {
		try {
			let response = await fetch(
				`http://localhost/a2/svelte-projects/car-rental-php/delete-item.php?id=${id}`,
				{
					credentials: 'include'
				}
			);
			let json = await response.json();
			cart = json;
			console.log(cart);
		} catch (error) {
			console.error('Error deleting cart data:', error);
		}
	}

	onMount(async () => {
		cars = await fetchCarData();
		await fetchCart();
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
		totalCost = total;
	}
</script>

<svelte:head>
	<title>Car Reservations</title>
	<meta name="description" content="List of car reservations" />
</svelte:head>

<section class="reserved-carts">
	<br />
	{#if cart.length > 0}
		<table>
			<tbody>
				<tr class="headings">
					<td> Item </td>
					<td> Price Per Day </td>
					<td> Rental Days </td>
					<td> Delete </td>
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
							<input
								type="number"
								name=""
								id=""
								bind:value={item.rental_days}
								min="1"
								max="365"
								on:change={() => updateRentalDays(item.id, item.rental_days)}
							/>
						</td>
						<td>
							<button on:click={() => deleteItem(item.id)}>Delete</button>
						</td>
					</tr>
				{/each}
				<tr>
					<td><hr /></td>
				</tr>
				<tr class="totalPrice">
					<td> Total Price: </td>
					<td />
					<td />
					<td>{totalCost} </td>
				</tr>
			</tbody>
		</table>
	{:else}
		You have no items in your cart
	{/if}
</section>
<br />
<button class="checkOut" on:click={() => validateCheckout()}>Check Out</button>

<style>
	.reserved-carts {
		display: flex;
		margin-top: 120px;
		justify-content: center;
		align-items: center;
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

	.checkOut {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 30%;
		margin: 0 auto;
		background-color: gray;
		color: #f0f4f7;
		border-radius: 10px;
		border: none;
		padding: 10px;
	}

	.checkOut:hover {
		background-color: #2b9f5a;
		color: #f0f4f7;
	}
</style>

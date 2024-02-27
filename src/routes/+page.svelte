<script>
	import { onMount } from 'svelte';
	import { fetchCarData } from '$lib/fetchCart';
	/**
	 * @type {any[]}
	 */
	let cars = [];

	/**
	 * @param {any} car
	 */
	async function addToCart(car) {
		if (car.disabled) return;
		if (car.availability == false) {
			alert('Sorry, the car is not available now. Please try other cars.');
		} else {
			car.disabled = true;
			cars = cars;
			await fetch(
				`http://localhost/a2/svelte-projects/car-rental-php/add-to-cart.php?id=${car.id}`,
				{
					method: 'GET',
					credentials: 'include'
					// headers: {
					// 	'Content-Type': 'application/json'
					// },
					// body: JSON.stringify({ id: car.id })
				}
			);
			alert('Successfully added to cart');
		}
	}

	onMount(async () => {
		cars = await fetchCarData();
	});
</script>

<svelte:head>
	<title>Home</title>
	<meta name="description" content="Svelte demo app" />
</svelte:head>

<section>
	<h1>Car Reservations</h1>
	<div class="grid">
		{#each cars as car (car.id)}
			<div class="item">
				<div class="image">
					<img src={car.img} alt={car.brand} class="carImg" />
				</div>
				<h3>{car.brand} {car.model} {car.year}</h3>
				<p class="carDetails">Mileage: {car.mileage}</p>
				<p class="carDetails">Fuel Type: {car.fuel_type}</p>
				<p class="carDetails">Seats: {car.seats}</p>
				<p class="carDetails">Price Per Day: {car.price_per_day}</p>
				<center>
					<p class="carDescription">{car.description}</p>
				</center>
				<button on:click={() => addToCart(car)}>
					{#if car.disabled === true}
						Added to cart
					{:else}
						Add to cart
					{/if}
				</button>
			</div>
		{/each}
	</div>
</section>

<style>
	section {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		flex: 0.6;
	}

	h1 {
		width: 100%;
	}

	h3 {
		margin-bottom: 5px;
	}

	.image {
		height: 130px;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		grid-gap: 50px;
	}

	.item {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		width: 100%;
		height: 100%;
		border-radius: 20px;
		border-color: gray;
		padding: 15px;
		box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.25);
		background-color: rgba(255, 255, 255, 0.15);
	}

	.carImg {
		width: 100%;
		height: auto;
		object-fit: contain;
	}

	button {
		background-color: #2b9f5a;
		border: none;
		color: white;
		padding: 12px 20px;
		text-decoration: none;
		display: inline-block;
		font-size: 18px;
		border-radius: 20px;
		box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.25);
		margin-top: auto;
	}

	.carDetails {
		margin-bottom: 0px;
		margin-top: 2px;
	}

	.carDescription {
		font-size: 14px;
		font-style: italic;
	}
</style>

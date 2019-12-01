import cloneDeep from 'lodash.clonedeep';

export const ACCEPTED_LOCALES = ['en', 'es'];
export const DEFAULT_LOCALE = 'es';

let displayData = null;
let gasData = null;

export const round = (value, exp) => {
	if (typeof exp === 'undefined' || +exp === 0)
		return Math.round(value);

	value = +value;
	exp = +exp;

	if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0))
		return NaN;

	// Shift
	value = value.toString().split('e');
	value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));

	// Shift back
	value = value.toString().split('e');
	return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
};

export const populatePage = () => {
	const exchangeRate = round(parseFloat(displayData.data.exchangeRate), 3).toFixed(3);
	const timestamp = new Date(displayData.data.timestamp).toLocaleDateString();
	document.getElementById('timestamp').innerHTML = timestamp;
	document.getElementById('exchangeRate').innerHTML = exchangeRate;

	displayData.data.prices = displayData.data.prices.filter(price => gasData.filters.municipios.includes(price.municipio) && gasData.filters.gasTypes.includes(price.gasType));
	const rows = displayData.data.prices.map(row => `<tr><td>${row.municipio}</td><td>${round(row.price, 3).toFixed(3)}</td><td>${row.gasType}</td></tr>`);
	document.getElementById('tableData').innerHTML = rows.join('\n');
};

export const convertCurrency = () => {
	const currency = document.forms.currencyForm.currency.value;
	const galToLiter = 3.78541;
	if (currency === 'peso') {
		displayData.data.prices = cloneDeep(gasData.data.prices);
		populatePage();
	} else if (currency === 'dollar') {
		const prices = gasData.data.prices.map(row => {
			const convertedPrice = round((row.price * galToLiter) / gasData.data.exchangeRate, 3).toFixed(3);
			return {municipio: row.municipio, price: convertedPrice, gasType: row.gasType};
		});
		displayData.data.prices = prices;
		populatePage();
	}
};

export const filterByGasType = () => {
	gasData.filters.gasTypes = [...document.forms.gasTypeForm.elements].map(input => input.checked ? input.value : undefined).filter(value => value !== undefined);
	displayData.data.prices = cloneDeep(gasData.data.prices);
	populatePage();
};

export const filterByMunicipio = () => {
	gasData.filters.municipios = [...document.forms.municipioForm.elements].map(input => input.checked ? input.value : undefined).filter(value => value !== undefined);
	displayData.data.prices = cloneDeep(gasData.data.prices);
	populatePage();
};

export const sortByField = (field) => {
	let prices = displayData.data.prices.sort((a, b) => (a[field] > b[field]) - (a[field] < b[field]));
	if (gasData.filters.lastField === field) {
		gasData.filters.reverse[field] = !gasData.filters.reverse[field];
		if (gasData.filters.reverse[field]) {
			prices = prices.reverse();
		}
	}
	gasData.filters.lastField = field;
	displayData.data.prices = prices;
	populatePage();
};

export const dismissLocale = () => {
	document.getElementById('localeLink').classList.add('d-none');
};

export const switchLocale = () => {
	const locale = Cookies.get('locale') || DEFAULT_LOCALE;
	const newLocale = ACCEPTED_LOCALES.find(currLocale => currLocale !== locale);
	fetch('/locale/', {
		method: 'POST',
		body: JSON.stringify({locale: newLocale}),
		headers: new Headers({
			'Content-type': 'application/json',
			'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
		})
	})
		.then(reply => reply.json())
		.then((reply) => {
			if(reply.status === 200) {
				Cookies.set('locale', newLocale, {expires: 30, path: '/'});
				window.location.reload();
			}
		});
};

export const fetchGasPrices = () => {
	fetch('/api/')
		.then(reply => reply.json())
		.then(reply => {
			if (reply.status === 200) {
				gasData = reply;
				gasData.filters = {};
				gasData.filters.gasTypes = [...new Set(gasData.data.prices.map(price => price.gasType))];
				gasData.filters.municipios = [...new Set(gasData.data.prices.map(price => price.municipio))];
				gasData.filters.reverse = {gasType: false, municipio: false, price: false};
				gasData.filters.lastField = undefined;
				displayData = cloneDeep(gasData);
				document.getElementById('gasData').classList.remove('d-none');
				document.getElementById('loading').classList.remove('d-block');
				document.getElementById('loading').classList.add('d-none');
				populatePage();
			}
		});
};

window.addEventListener('DOMContentLoaded', () => {
	const tableHeaders = document.getElementsByTagName("th");
	for (let tableHeader of tableHeaders) {
		tableHeader.addEventListener('click', () => sortByField(tableHeader.dataset.fieldName));
	}

	[...document.forms.currencyForm.elements].forEach(currencyRadio => currencyRadio.addEventListener('change', convertCurrency));
	[...document.forms.gasTypeForm.elements].forEach(gasTypeCheckbox => gasTypeCheckbox.addEventListener('change', filterByGasType));
	[...document.forms.municipioForm.elements].forEach(municipioCheckbox => municipioCheckbox.addEventListener('change', filterByMunicipio));
	document.getElementById('localeLink').addEventListener('click', switchLocale);
	// document.getElementById('localeDismiss').addEventListener('click', dismissLocale);
	fetchGasPrices();
});

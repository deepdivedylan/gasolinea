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

export const sortByField = (field) => {
	const prices = displayData.data.prices.sort((a, b) => (a[field] > b[field]) - (a[field] < b[field]));
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
				displayData = cloneDeep(gasData);
				document.getElementById('gasData').classList.remove('d-none');
				document.getElementById('loading').classList.remove('d-block');
				document.getElementById('loading').classList.add('d-none');
				populatePage();
			}
		});
};

window.addEventListener('DOMContentLoaded', () => {
	const currencyRadios = document.getElementsByTagName('input');
	for (let currencyRadio of currencyRadios) {
		currencyRadio.addEventListener('change', convertCurrency);
	}
	const tableHeaders = document.getElementsByTagName("th");
	for (let tableHeader of tableHeaders) {
		tableHeader.addEventListener('click', () => sortByField(tableHeader.dataset.fieldName));
	}
	document.getElementById('localeLink').addEventListener('click', switchLocale);
	document.getElementById('localeDismiss').addEventListener('click', dismissLocale);
	fetchGasPrices();
});

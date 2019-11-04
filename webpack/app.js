export const ACCEPTED_LOCALES = ['en', 'es'];
export const DEFAULT_LOCALE = 'es';

export const switchLocale = () => {
	const locale = Cookies.get('locale') || DEFAULT_LOCALE;
	const newLocale = ACCEPTED_LOCALES.find(currLocale => currLocale !== locale);
	fetch('/api/locale/', {
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

window.addEventListener('DOMContentLoaded', () => {
	document.getElementById('localeLink').addEventListener('click', switchLocale);
});

import 'popper.js';
import 'jquery';
import 'bootstrap';
import 'js-cookie';

// loading FontAwesome like this reduces page loads by 1 MB :scream_cat:
import { dom, library } from '@fortawesome/fontawesome-svg-core';
import { faGasPump, faMoneyBillWave, faSort, faSpinner } from '@fortawesome/free-solid-svg-icons';
import { faGithub } from '@fortawesome/free-brands-svg-icons';
library.add(faGasPump);
library.add(faGithub);
library.add(faMoneyBillWave);
library.add(faSort);
library.add(faSpinner);
dom.watch();
import 'popper.js';
import 'jquery';
import 'bootstrap';
import 'js-cookie';

// loading FontAwesome like this reduces page loads by 1 MB :scream_cat:
import { dom, library } from '@fortawesome/fontawesome-svg-core';
import { faCity, faCog, faGasPump, faInfo, faLanguage, faMoneyBillWave, faOilCan, faSort, faSpinner } from '@fortawesome/free-solid-svg-icons';
import { faGithub, faReddit } from '@fortawesome/free-brands-svg-icons';
library.add(faCity);
library.add(faCog);
library.add(faGasPump);
library.add(faGithub);
library.add(faInfo);
library.add(faLanguage);
library.add(faMoneyBillWave);
library.add(faOilCan);
library.add(faReddit);
library.add(faSort);
library.add(faSpinner);
dom.watch();
import 'popper.js';
import 'jquery';
import 'bootstrap';
import 'js-cookie';

// loading FontAwesome like this reduces page loads by 1 MB :scream_cat:
import { dom, library } from '@fortawesome/fontawesome-svg-core';
import { faBan, faBalanceScale, faBars, faEnvelope, faHandshake, faHome, faPaperPlane, faPassport, faPencilAlt, faUser } from '@fortawesome/free-solid-svg-icons';
library.add(faBalanceScale);
library.add(faBan);
library.add(faBars);
library.add(faEnvelope);
library.add(faHandshake);
library.add(faHome);
library.add(faPaperPlane);
library.add(faPassport);
library.add(faPencilAlt);
library.add(faUser);
dom.watch();
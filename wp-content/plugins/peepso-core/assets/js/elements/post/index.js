import save from './save';
import track from './track';
import human from './human';
import linkTarget from './link-target';

function init() {
	save.init();
	track.init();
	human.init();
	linkTarget.init();
}

export default { init };

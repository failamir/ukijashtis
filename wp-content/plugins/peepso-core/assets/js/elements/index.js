import $ from 'jquery';
import peepso from 'peepso';
import droppable from './droppable';
import post from './post';
import hashtag from './hashtag';
import mention from './mention';
import permalink from './permalink';

peepso.elements = {
	droppable
};

$(function() {
	post.init();
	hashtag.init();
	mention.init();
	permalink.init();
});

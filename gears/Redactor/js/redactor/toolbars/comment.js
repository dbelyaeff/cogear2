var RTOOLBAR =
{
	html:
	{
		title: RLANG.html,
		func: 'toggle',
		separator: true
	},
	bold:
	{
		title: RLANG.bold,
		exec: 'bold',
	 	param: null
	},
	italic:
	{
		title: RLANG.italic,
		exec: 'italic',
	 	param: null
	},
	deleted:
	{
		title: RLANG.deleted,
		exec: 'strikethrough',
	 	param: null,
		separator: true
	},
	insertunorderedlist:
	{
		title: '&bull; ' + RLANG.unorderedlist,
		exec: 'insertunorderedlist',
	 	param: null
	},
	insertorderedlist:
	{
		title: '1. ' + RLANG.orderedlist,
		exec: 'insertorderedlist',
	 	param: null
	},
	outdent:
	{
		title: '< ' + RLANG.outdent,
		exec: 'outdent',
	 	param: null
	},
	indent:
	{
		title: '> ' + RLANG.indent,
		exec: 'indent',
	 	param: null,
		separator: true
	},
	image:
	{
		title: RLANG.image,
		func: 'showImage'
	},
	video:
	{
		title: RLANG.video,
		func: 'showVideo'
	},
	link:
	{
		title: RLANG.link,
		func: 'show',
		dropdown:
		{
			link:
			{
				title: RLANG.link_insert,
				func: 'showLink'
			},
			unlink:
			{
				title: RLANG.unlink,
				exec: 'unlink',
			 	param: null
			}
		},
		separator: true
	}
};
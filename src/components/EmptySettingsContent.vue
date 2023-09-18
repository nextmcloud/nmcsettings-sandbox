<template>
	<div id="app-content"></div>
</template>

<script>

export default {
	props: {
		url: {
			type: String,
			required: true,
		},
	},
	methods: {
		redirect() {
			window.location.href = this.url
		},
	},
	mounted () {
		this.redirect()
	},
}
</script>

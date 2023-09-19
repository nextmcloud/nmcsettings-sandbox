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
	mounted() {
		this.redirect()
	},
	methods: {
		redirect() {
			// window.location.href = this.url
		},
	},
}
</script>

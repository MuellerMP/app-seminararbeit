<template>
	<div id="content" class="seminararbeit">
		<AppNavigation>
			<AppNavigationNew v-if="!loading"
				:text="t('seminararbeit', 'New text')"
				:disabled="false"
				button-id="new-seminararbeit-button"
				button-class="icon-add"
				@click="newText" />
			<ul>
				<AppNavigationItem v-for="text in texts"
					:key="text.id"
					:title="text.title ? text.title : t('seminararbeit', 'New text')"
					:class="{active: currentTextId === text.id}"
					@click="openText(text)">
					<template slot="actions">
						<ActionButton v-if="text.id === -1"
							icon="icon-close"
							@click="cancelNewText(text)">
							{{ t('seminararbeit', 'Cancel text creation') }}
						</ActionButton>
						<ActionButton v-else
							icon="icon-delete"
							@click="deleteText(text)">
							{{ t('seminararbeit', 'Delete text') }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul>
		</AppNavigation>
		<AppContent>
			<div v-if="currentText">
				<input ref="title"
					v-model="currentText.title"
					type="text"
					:disabled="updating">
				<input ref="filename"
					v-model="currentText.filename"
					type="text"
					:disabled="updating">
				<v-md-editor ref="content"
					v-model="currentText.content"
					height="400px"
					:disabled="updating" />
				<input type="button"
					class="primary"
					:value="t('seminararbeit', 'Save')"
					:disabled="updating || !savePossible"
					@click="saveText">
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('seminararbeit', 'Create a text to get started') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'

import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
	},
	data: function() {
		return {
			texts: [],
			currentTextId: null,
			updating: false,
			loading: true,
		}
	},
	computed: {
		/**
		 * Return the currently selected text object
		 * @returns {Object|null}
		 */
		currentText() {
			if (this.currentTextId === null) {
				return null
			}
			return this.texts.find((text) => text.id === this.currentTextId)
		},

		/**
		 * Returns true if a text is selected and its title is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentText && this.currentText.title !== '' && this.currentText.filename !== ''
		},
	},
	/**
	 * Fetch list of texts when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(OC.generateUrl('/apps/seminararbeit/texts'))
			this.texts = response.data
		} catch (e) {
			console.error(e)
			OCP.Toast.error(t('seminararbeit', 'Could not fetch texts'))
		}
		this.loading = false
	},

	methods: {
		/**
		 * Create a new text and focus the text content field automatically
		 * @param {Object} text Text object
		 */
		openText(text) {
			if (this.updating) {
				return
			}
			this.currentTextId = text.id
			this.$nextTick(() => {
				this.$refs.content.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new text or save
		 */
		saveText() {
			if (this.currentTextId === -1) {
				this.createText(this.currentText)
			} else {
				this.updateText(this.currentText)
			}
		},
		/**
		 * Create a new text and focus the text content field automatically
		 * The text is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newText() {
			if (this.currentTextId !== -1) {
				this.currentTextId = -1
				this.texts.push({
					id: -1,
					title: '',
					filename: '',
					content: '',
				})
				this.$nextTick(() => {
					this.$refs.title.focus()
				})
			}
		},
		/**
		 * Abort creating a new text
		 */
		cancelNewText() {
			this.texts.splice(this.texts.findIndex((text) => text.id === -1), 1)
			this.currentTextId = null
		},
		/**
		 * Create a new text by sending the information to the server
		 * @param {Object} text Text object
		 */
		async createText(text) {
			this.updating = true
			try {
				const response = await axios.post(OC.generateUrl(`/apps/seminararbeit/texts`), text)
				const index = this.texts.findIndex((match) => match.id === this.currentTextId)
				this.$set(this.texts, index, response.data)
				this.currentTextId = response.data.id
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('seminararbeit', 'Could not create the text'))
			}
			this.updating = false
		},
		/**
		 * Update an existing text on the server
		 * @param {Object} text Text object
		 */
		async updateText(text) {
			this.updating = true
			try {
				await axios.put(OC.generateUrl(`/apps/seminararbeit/texts/${text.id}`), text)
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('seminararbeit', 'Could not update the text'))
			}
			this.updating = false
		},
		/**
		 * Delete a text, remove it from the frontend and show a hint
		 * @param {Object} text Text object
		 */
		async deleteText(text) {
			try {
				await axios.delete(OC.generateUrl(`/apps/seminararbeit/texts/${text.id}`))
				this.texts.splice(this.texts.indexOf(text), 1)
				if (this.currentTextId === text.id) {
					this.currentTextId = null
				}
				OCP.Toast.success(t('seminararbeit', 'Text deleted'))
			} catch (e) {
				console.error(e)
				OCP.Toast.error(t('seminararbeit', 'Could not delete the text'))
			}
		},
	},
}
</script>
<style scoped>
	#app-content > div {
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	input[type='text'] {
		width: 100%;
	}

	textarea {
		flex-grow: 1;
		width: 100%;
	}
</style>

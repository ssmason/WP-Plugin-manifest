/**
 * Satori Manifest — Admin options page JavaScript.
 *
 * Handles the JS-driven Lists tab UI: adding, saving, deleting and
 * reordering sections and their items via AJAX. Uses vanilla JS only.
 *
 * @package
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

/* global satoriManifestAdmin */

(function () {
	'use strict';

	/**
	 * Shorthand for document.querySelector.
	 *
	 * @param {string} selector CSS selector.
	 * @return {Element|null} Matched element.
	 */
	const $ = (selector) => document.querySelector(selector);

	/**
	 * Shorthand for document.querySelectorAll.
	 *
	 * @param {string}  selector  CSS selector.
	 * @param {Element} [context] Optional parent element.
	 * @return {NodeList} Matched elements.
	 */
	const $$ = (selector, context = document) =>
		context.querySelectorAll(selector);

	const { ajaxUrl, nonces, i18n } = satoriManifestAdmin;

	// ── Utilities ──────────────────────────────────────────────────────────────

	/**
	 * Sends an AJAX POST request and resolves with parsed JSON.
	 *
	 * @param {string} action wp_ajax_ action name.
	 * @param {Object} data   Additional POST data.
	 * @return {Promise<Object>} Parsed response object.
	 */
	async function ajax(action, data) {
		const body = new FormData();
		body.append('action', action);

		for (const [key, value] of Object.entries(data)) {
			body.append(key, value);
		}

		const response = await fetch(ajaxUrl, { method: 'POST', body });
		return response.json();
	}

	/**
	 * Shows a notice message in the admin UI.
	 *
	 * @param {string}  message   Notice text.
	 * @param {boolean} isSuccess True for success, false for error.
	 */
	function showNotice(message, isSuccess = true) {
		const notice = $('#satori-manifest-notice');
		if (!notice) {
			return;
		}

		notice.textContent = message;
		notice.className =
			'satori-manifest-lists__notice ' +
			(isSuccess ? 'is-success' : 'is-error');
		notice.hidden = false;

		setTimeout(() => {
			notice.hidden = true;
		}, 4000);
	}

	/**
	 * Collects item data from a section's item rows.
	 *
	 * @param {Element} sectionEl The section list item element.
	 * @return {Array<Object>} Array of item data objects.
	 */
	function collectItems(sectionEl) {
		return Array.from($$('.satori-manifest-item', sectionEl)).map(
			(row, index) => ({
				label:
					row.querySelector('.satori-manifest-item__label')?.value ??
					'',
				description:
					row.querySelector('.satori-manifest-item__description')
						?.value ?? '',
				price_prefix:
					row.querySelector('.satori-manifest-item__prefix')?.value ??
					'',
				price:
					row.querySelector('.satori-manifest-item__price')?.value ??
					'0.00',
				sort_order: index,
			})
		);
	}

	// ── Section save ───────────────────────────────────────────────────────────

	/**
	 * Handles a click on a section's Save button.
	 *
	 * @param {Element} sectionEl The section list item element.
	 */
	async function handleSectionSave(sectionEl) {
		const postId = parseInt(sectionEl.dataset.postId ?? '0', 10);
		const title =
			sectionEl.querySelector('.satori-manifest-section__title-input')
				?.value ?? '';
		const items = collectItems(sectionEl);
		const order = Array.from($$('.satori-manifest-section')).indexOf(
			sectionEl
		);

		const btn = sectionEl.querySelector('.satori-manifest-section__save');
		if (btn) {
			btn.textContent = i18n.saving;
			btn.disabled = true;
		}

		const result = await ajax('satori_manifest_save_section', {
			nonce: nonces.saveSection,
			post_id: postId,
			title,
			items: JSON.stringify(items),
			sort_order: order,
		});

		if (btn) {
			btn.textContent = i18n.saved;
			btn.disabled = false;
			setTimeout(() => {
				btn.textContent = 'Save';
			}, 2000);
		}

		if (result.success) {
			sectionEl.dataset.postId = result.data.post_id;
			showNotice(result.data.message);
		} else {
			showNotice(result.data?.message ?? i18n.error, false);
		}
	}

	// ── Section delete ─────────────────────────────────────────────────────────

	/**
	 * Handles a click on a section's Delete button.
	 *
	 * @param {Element} sectionEl The section list item element.
	 */
	async function handleSectionDelete(sectionEl) {
		// eslint-disable-next-line no-alert
		if (!window.confirm(i18n.confirmDelete)) {
			return;
		}

		const postId = parseInt(sectionEl.dataset.postId ?? '0', 10);

		if (postId > 0) {
			const result = await ajax('satori_manifest_delete_section', {
				nonce: nonces.deleteSection,
				post_id: postId,
			});

			if (!result.success) {
				showNotice(result.data?.message ?? i18n.error, false);
				return;
			}

			showNotice(result.data.message);
		}

		sectionEl.remove();
	}

	// ── Add section ────────────────────────────────────────────────────────────

	/**
	 * Clones the new-section template and prepends it to the list.
	 */
	function handleAddSection() {
		const template = $('#satori-manifest-section-template');
		const list = $('#satori-manifest-section-list');

		if (!template || !list) {
			return;
		}

		const clone = template.content.cloneNode(true);
		list.prepend(clone);

		// Focus the new title input.
		list.firstElementChild
			?.querySelector('.satori-manifest-section__title-input')
			?.focus();
	}

	// ── Add item row ───────────────────────────────────────────────────────────

	/**
	 * Clones the item template and appends it to a section's tbody.
	 *
	 * @param {Element} sectionEl The section list item element.
	 */
	function handleAddItem(sectionEl) {
		const template = $('#satori-manifest-item-template');
		const tbody = sectionEl.querySelector('.satori-manifest-items__body');

		if (!template || !tbody) {
			return;
		}

		const clone = template.content.cloneNode(true);
		tbody.appendChild(clone);
		tbody.lastElementChild?.querySelector('input')?.focus();
	}

	// ── Remove item row ────────────────────────────────────────────────────────

	/**
	 * Removes an item row from the table.
	 *
	 * @param {Element} rowEl The <tr> element to remove.
	 */
	function handleRemoveItem(rowEl) {
		rowEl.remove();
	}

	// ── Pattern customise / restore ────────────────────────────────────────────

	/**
	 * Sends a pattern customise request.
	 *
	 * @param {string} handle Pattern handle slug.
	 * @param {string} title  Pattern title.
	 */
	async function handleCustomisePattern(handle, title) {
		const result = await ajax('satori_manifest_save_pattern', {
			nonce: nonces.savePattern,
			handle,
			title,
			content: '',
		});

		const notice = $('#satori-manifest-pattern-notice');

		if (result.success) {
			if (notice) {
				notice.textContent = result.data.message;
				notice.className = 'satori-manifest-notice is-success';
				notice.hidden = false;
			}
			// Reload to reflect the change.
			window.location.reload();
		} else if (notice) {
			notice.textContent = result.data?.message ?? i18n.error;
			notice.className = 'satori-manifest-notice is-error';
			notice.hidden = false;
		}
	}

	// ── Delegated event binding ────────────────────────────────────────────────

	document.addEventListener('click', async (event) => {
		const target = /** @type {Element} */ (event.target);

		// Add section.
		if (target.closest('#satori-manifest-add-section')) {
			handleAddSection();
			return;
		}

		// Save section.
		const saveBtn = target.closest('.satori-manifest-section__save');
		if (saveBtn) {
			const sectionEl = saveBtn.closest('.satori-manifest-section');
			if (sectionEl) {
				await handleSectionSave(sectionEl);
			}
			return;
		}

		// Delete section.
		const deleteBtn = target.closest('.satori-manifest-section__delete');
		if (deleteBtn) {
			const sectionEl = deleteBtn.closest('.satori-manifest-section');
			if (sectionEl) {
				await handleSectionDelete(sectionEl);
			}
			return;
		}

		// Add item row.
		const addItemBtn = target.closest('.satori-manifest-items__add-row');
		if (addItemBtn) {
			const sectionEl = addItemBtn.closest('.satori-manifest-section');
			if (sectionEl) {
				handleAddItem(sectionEl);
			}
			return;
		}

		// Remove item row.
		const removeBtn = target.closest('.satori-manifest-item__remove');
		if (removeBtn) {
			const rowEl = removeBtn.closest('.satori-manifest-item');
			if (rowEl) {
				handleRemoveItem(rowEl);
			}
			return;
		}

		// Customise pattern.
		const customiseBtn = target.closest(
			'.satori-manifest-pattern__customise'
		);
		if (customiseBtn) {
			const handle = customiseBtn.dataset.handle ?? '';
			const title = customiseBtn.dataset.title ?? '';
			await handleCustomisePattern(handle, title);
		}
	});
})();

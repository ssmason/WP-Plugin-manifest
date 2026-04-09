/**
 * Satori Manifest — Meta box JavaScript.
 *
 * Handles the sections & items UI on the sm_manifest CPT edit screen.
 * No AJAX — sections are serialised to a hidden field and saved via the
 * standard WP post form (Publish / Update button).
 *
 * Architecture notes:
 * - All button events use delegated listeners on document rather than direct
 *   bindings. This is intentional: sections and items are added dynamically
 *   after page load by cloning <template> elements, so elements do not exist
 *   at DOMContentLoaded time and cannot be bound directly.
 * - serialise() is debounced on text input (300 ms) to avoid writing JSON on
 *   every keypress. Structural actions (add/remove/reorder) call it directly
 *   for immediate consistency.
 * - Section removal uses a two-step inline confirmation so no blocking
 *   window.confirm() dialog is required.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

( function () {
	'use strict';

	// ── DOM helpers ────────────────────────────────────────────────────────────

	/**
	 * @param {string}   sel      CSS selector.
	 * @param {Element=} context  Optional context (defaults to document).
	 * @return {Element|null}
	 */
	const $ = ( sel, context = document ) => context.querySelector( sel );

	/**
	 * @param {string}   sel      CSS selector.
	 * @param {Element=} context  Optional context (defaults to document).
	 * @return {NodeList}
	 */
	const $$ = ( sel, context = document ) => context.querySelectorAll( sel );

	// ── Serialise ──────────────────────────────────────────────────────────────

	/**
	 * Reads item rows from a section element and returns plain objects.
	 *
	 * @param {Element} sectionEl
	 * @return {Array<Object>}
	 */
	function collectItems( sectionEl ) {
		return Array.from( $$( '.satori-manifest-item', sectionEl ) ).map(
			( row, idx ) => ( {
				label:        row.querySelector( '.satori-manifest-item__label' )?.value        ?? '',
				description:  row.querySelector( '.satori-manifest-item__description' )?.value  ?? '',
				price_prefix: row.querySelector( '.satori-manifest-item__prefix' )?.value       ?? '',
				price:        row.querySelector( '.satori-manifest-item__price' )?.value        ?? '',
				sort_order:   idx,
			} )
		);
	}

	/**
	 * Serialises all sections from the DOM into the hidden JSON field.
	 * Called on structural mutations and (debounced) on text input.
	 *
	 * @return {void}
	 */
	function serialise() {
		const field = $( '#satori-manifest-sections-data' );
		if ( ! field ) {
			return;
		}

		const sections = Array.from(
			$$( '.satori-manifest-section', $( '#satori-manifest-sections-list' ) )
		).map( ( sectionEl, idx ) => ( {
			title:      sectionEl.querySelector( '.satori-manifest-section__title-input' )?.value ?? '',
			sort_order: idx,
			items:      collectItems( sectionEl ),
		} ) );

		field.value = JSON.stringify( sections );
	}

	// Serialise before WP submits the post form so the hidden field is current.
	const postForm = $( '#post' );
	if ( postForm ) {
		postForm.addEventListener( 'submit', serialise );
	}

	// Debounced serialise for text input — avoids writing JSON on every keypress.
	let serialiseTimer = null;

	/**
	 * Schedules a serialise() call 300 ms after the last input event.
	 *
	 * @return {void}
	 */
	function serialiseDebounced() {
		clearTimeout( serialiseTimer );
		serialiseTimer = setTimeout( serialise, 300 );
	}

	// ── Add section ────────────────────────────────────────────────────────────

	/**
	 * Clones the section template and appends it to the sections list.
	 *
	 * @return {void}
	 */
	function addSection() {
		const template  = $( '#satori-manifest-section-template' );
		const container = $( '#satori-manifest-sections-list' );

		if ( ! template || ! container ) {
			return;
		}

		const clone = template.content.cloneNode( true );
		container.appendChild( clone );
		container.lastElementChild
			?.querySelector( '.satori-manifest-section__title-input' )
			?.focus();

		serialise();
	}

	// ── Remove section ─────────────────────────────────────────────────────────

	/**
	 * Two-step inline confirmation before removing a section.
	 *
	 * First click changes the button text to a confirmation prompt.
	 * A second click within 3 seconds removes the section. If the user
	 * does not confirm, the button resets automatically after the timeout.
	 * This avoids the blocking window.confirm() dialog.
	 *
	 * @param {Element} btn  The remove button that was clicked.
	 * @return {void}
	 */
	function handleRemoveSectionClick( btn ) {
		if ( btn.dataset.confirming ) {
			// Second click — confirmed, proceed with removal.
			const sectionEl = btn.closest( '.satori-manifest-section' );
			if ( sectionEl ) {
				sectionEl.remove();
				serialise();
			}
			return;
		}

		// First click — enter confirming state.
		btn.dataset.confirming = '1';
		btn.textContent = 'Sure? Click again to remove';

		setTimeout( () => {
			// Auto-reset if the user does not confirm within 3 seconds.
			if ( btn.isConnected ) {
				delete btn.dataset.confirming;
				btn.textContent = '\u00d7 Remove';
			}
		}, 3000 );
	}

	// ── Add item ───────────────────────────────────────────────────────────────

	/**
	 * Clones the item template and appends it to a section's tbody.
	 *
	 * @param {Element} sectionEl
	 * @return {void}
	 */
	function addItem( sectionEl ) {
		const template = $( '#satori-manifest-item-template' );
		const tbody    = sectionEl.querySelector( '.satori-manifest-items__body' );

		if ( ! template || ! tbody ) {
			return;
		}

		const clone = template.content.cloneNode( true );
		tbody.appendChild( clone );
		tbody.lastElementChild?.querySelector( 'input' )?.focus();

		serialise();
	}

	// ── Remove item ────────────────────────────────────────────────────────────

	/**
	 * Removes an item row.
	 *
	 * @param {Element} rowEl  The <tr> element.
	 * @return {void}
	 */
	function removeItem( rowEl ) {
		rowEl.remove();
		serialise();
	}

	// ── Move section up / down ─────────────────────────────────────────────────

	/**
	 * Moves a section one position up in the list.
	 *
	 * @param {Element} sectionEl
	 * @return {void}
	 */
	function moveUp( sectionEl ) {
		const prev = sectionEl.previousElementSibling;
		if ( prev ) {
			sectionEl.parentNode.insertBefore( sectionEl, prev );
			serialise();
		}
	}

	/**
	 * Moves a section one position down in the list.
	 *
	 * @param {Element} sectionEl
	 * @return {void}
	 */
	function moveDown( sectionEl ) {
		const next = sectionEl.nextElementSibling;
		if ( next ) {
			sectionEl.parentNode.insertBefore( next, sectionEl );
			serialise();
		}
	}

	// ── Move item up / down ────────────────────────────────────────────────────

	/**
	 * Moves an item row one position up in its tbody.
	 *
	 * @param {Element} rowEl  The <tr> element.
	 * @return {void}
	 */
	function moveItemUp( rowEl ) {
		const prev = rowEl.previousElementSibling;
		if ( prev ) {
			rowEl.parentNode.insertBefore( rowEl, prev );
			serialise();
		}
	}

	/**
	 * Moves an item row one position down in its tbody.
	 *
	 * @param {Element} rowEl  The <tr> element.
	 * @return {void}
	 */
	function moveItemDown( rowEl ) {
		const next = rowEl.nextElementSibling;
		if ( next ) {
			rowEl.parentNode.insertBefore( next, rowEl );
			serialise();
		}
	}

	// ── Toggle section body ────────────────────────────────────────────────────

	/**
	 * Collapses or expands a section's item table.
	 *
	 * Toggles aria-expanded on the button and is-collapsed on the body div.
	 *
	 * @param {Element} sectionEl
	 * @return {void}
	 */
	function toggleSection( sectionEl ) {
		const body   = sectionEl.querySelector( '.satori-manifest-section__body' );
		const toggle = sectionEl.querySelector( '.satori-manifest-section__toggle' );
		if ( ! body || ! toggle ) {
			return;
		}
		const isExpanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
		toggle.setAttribute( 'aria-expanded', isExpanded ? 'false' : 'true' );
		body.classList.toggle( 'is-collapsed', isExpanded );
	}

	// ── Input change → serialise (debounced) ───────────────────────────────────

	// Keep the hidden field in sync as the user types, debounced to avoid
	// writing JSON on every keypress for large manifests.
	const metabox = $( '.satori-manifest-metabox' );
	if ( metabox ) {
		metabox.addEventListener( 'input', serialiseDebounced );
	}

	// ── Delegated click handler ────────────────────────────────────────────────

	// A single delegated listener handles all button clicks. Delegation is used
	// because sections and items are cloned from <template> elements at runtime
	// and do not exist when this script first executes.
	document.addEventListener( 'click', ( e ) => {
		const target = /** @type {Element} */ ( e.target );

		// Add section.
		if ( target.closest( '.satori-manifest-add-section' ) ) {
			addSection();
			return;
		}

		// Remove section (two-step confirmation).
		const removeSecBtn = target.closest( '.satori-manifest-section__remove' );
		if ( removeSecBtn ) {
			handleRemoveSectionClick( removeSecBtn );
			return;
		}

		// Add item row.
		const addItemBtn = target.closest( '.satori-manifest-items__add-row' );
		if ( addItemBtn ) {
			const sec = addItemBtn.closest( '.satori-manifest-section' );
			if ( sec ) {
				addItem( sec );
			}
			return;
		}

		// Remove item row.
		const removeItemBtn = target.closest( '.satori-manifest-item__remove' );
		if ( removeItemBtn ) {
			const row = removeItemBtn.closest( '.satori-manifest-item' );
			if ( row ) {
				removeItem( row );
			}
			return;
		}

		// Move item up.
		const moveItemUpBtn = target.closest( '.satori-manifest-item__move-up' );
		if ( moveItemUpBtn ) {
			const row = moveItemUpBtn.closest( '.satori-manifest-item' );
			if ( row ) {
				moveItemUp( row );
			}
			return;
		}

		// Move item down.
		const moveItemDownBtn = target.closest( '.satori-manifest-item__move-down' );
		if ( moveItemDownBtn ) {
			const row = moveItemDownBtn.closest( '.satori-manifest-item' );
			if ( row ) {
				moveItemDown( row );
			}
			return;
		}

		// Move section up.
		const moveUpBtn = target.closest( '.satori-manifest-section__move-up' );
		if ( moveUpBtn ) {
			const sec = moveUpBtn.closest( '.satori-manifest-section' );
			if ( sec ) {
				moveUp( sec );
			}
			return;
		}

		// Move section down.
		const moveDownBtn = target.closest( '.satori-manifest-section__move-down' );
		if ( moveDownBtn ) {
			const sec = moveDownBtn.closest( '.satori-manifest-section' );
			if ( sec ) {
				moveDown( sec );
			}
			return;
		}

		// Toggle section body open/closed.
		const toggleBtn = target.closest( '.satori-manifest-section__toggle' );
		if ( toggleBtn ) {
			const sec = toggleBtn.closest( '.satori-manifest-section' );
			if ( sec ) {
				toggleSection( sec );
			}
		}
	} );
} )();

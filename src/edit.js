/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */

import { PanelBody, TextControl, Button, SearchControl, PanelRow, ComboboxControl } from '@wordpress/components';
import { react, useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';


export default function Edit({ attributes, setAttributes }) {

	const { searchTerm, resortFind, selectedOption } = attributes;
	const [mySuggession, setMySuggestions] = useState([]);
	const [loading, setLoading] = useState(false);

	//For ComboBoxControl:
	const [ options, setOptions ] = useState([]);
	const [ search, setSearch ] = useState('');

	useEffect(() => {
		if(search && search.length > 1){
			setLoading(true);
			apiFetch({ path: `/fnugg/v1/autocomplete?q=${search}` })
			.then((response) => {
				JSON.stringify(setOptions(response));
				setLoading(false);
			})
			.catch(() => {				
				setLoading(false);
			});
		}
	}, [search]);





	// const autoCoplete = () => {		
	// 	setLoading(true);
	// 	apiFetch({ path: `/fnugg/v1/autocomplete?q=${searchTerm}` })
	// 		.then((results) => {
	// 			JSON.stringify(setMySuggestions(results));
	// 			setLoading(false);
	// 		})
	// 		.catch(() => {
	// 			setLoading(false);
	// 		});
	// };

	const fetchResortData = () => {
		setLoading(true);
		apiFetch({ path: `/fnugg/v1/search?q=${resortFind}` })
			.then((data) => {
				JSON.stringify(setMySuggestions(data));
				setLoading(false);
			})
			.catch(() => {
				setLoading(false);
			});
	};

	// useEffect(() => { 
	// 	//This && is truely learning curve//Wihout it, 
	// 	//error shows length is typing error because no data in searchTerm.
	// 	if (searchTerm && searchTerm.length > 1) {
	// 		autoCoplete();
	// 	}
	// }, [searchTerm]);

	useEffect(() => {
		if (resortFind && resortFind.length > 0) {
			fetchResortData()
		}
	}, [resortFind]);
	 const myData = JSON.stringify(mySuggession.result);
	 const myOptions = JSON.stringify(options.result);
	
	return (
		<>

			<p {...useBlockProps()}>

				<InspectorControls>
					<PanelBody title="AutoComplete Ski Resort">
						<TextControl label={__('AutoComplete Ski Resort')}
							value={searchTerm}
							onChange={(value) => setAttributes({ searchTerm: value })}
						/>


						<PanelRow>
							<div style={{ "height": "auto", "width": "100%", "backgroundColor": "#c2c2c2", padding: "5px" }}>

								{
									myData ?
										<p>{myData.replace(/[\[\]{}"\/]/g, '')}</p>
										: ''
								}

								<br /> <br />
								{/* Description: {JSON.stringify(mySuggession.description)} */}

								{JSON.stringify(mySuggession.total)}
							</div>
						</PanelRow>
					</PanelBody>
					<PanelBody title="Search Ski Resort">

						<TextControl label={__('Search Ski Resort')}
							value={resortFind}
							onChange={(value) => setAttributes({ resortFind: value })}
						/>

						<PanelRow>
							<div style={{ "height": "auto", "width": "100%", "backgroundColor": "#c2c2c2", padding: "5px" }}>
								{mySuggession ?
									<p>Name: {mySuggession.name}<br />
									   Description: {mySuggession.description}<br />
									   Lift Open: {JSON.stringify(mySuggession.lifts)}
								
									</p>
									: ''}
							</div>

						</PanelRow>

					</PanelBody>
				</InspectorControls>
				{/* <SearchControl
					label="Search Resort"
					value={searchTerm}
					onChange={(value) => setSearchTerm(value)}
					help="Type to search for a ski resort."
				/> */}
				<div style={{ "color": "black", "height": "auto", "width": "100%", "backgroundColor": "#c2c2c2", paddingLeft: "5px" }}>
					{/* {JSON.stringify(mySuggession.result)} <br /><br /> */}
					{/* Description: {JSON.stringify(mySuggession.description)}<br />
					Lift Count: {JSON.stringify(mySuggession.lifts)}<br /> */}
					{/* Lift Open: {JSON.stringify(mySuggession.lifts.open)} */}
					{
						myData ?
							<p>{myData.replace(/[\[\]{}"\/]/g, '')} <br /></p>
							: ''
					}

					
				</div>
				<ComboboxControl
					label="Search Resort final:"
					value={selectedOption}
					// options={options.map((option) => ({
					// 	value: option.name,
					// 	label: option.name
					// }))}
					options={ myOptions["name"]
					}
					onChange={(value) => setAttributes({selectedOption: value})}
					
										
				/>
			</p>
		</>
	);
}

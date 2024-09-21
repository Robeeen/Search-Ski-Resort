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

import { PanelBody, TextControl, SearchControl, PanelRow, ComboboxControl } from '@wordpress/components';
import { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';


export default function Edit({ attributes, setAttributes }) {

	const { resortFind, selectedOption } = attributes;	
	const [loading, setLoading] = useState(false);

	//For ComboBoxControl: Autocomplete - search
	const [ options, setOptions ] = useState([]);
	const [ search, setSearch ] = useState('');

	useEffect(() => {
		if(search.length >= 2){
			apiFetch({ path: `/fnugg/v1/autocomplete?q=${search}` })
			.then((response) => {
				console.log('Api Response on search', response.result);
				const formattedOption = Array.isArray(response.result)
				? response.result.map((item) => ({
					value: item.name,
					label: item.name
				}))
				: [];
				console.log('Formattted Option:', formattedOption);
				setOptions(formattedOption);
			})
			.catch(() => {	
				setOptions([]);		
			});
		}
	}, [search]);

	
	//For TextControl: Search on Panel Row
	const [mySuggession, setMySuggestions] = useState([]);
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
	useEffect(() => {
		if (resortFind && resortFind.length > 0) {
			fetchResortData()
		}
	}, [resortFind]);
	
	return (
		<>
			<p {...useBlockProps()}>

				<InspectorControls>
					<PanelBody title="Search Ski Resort">
						<TextControl 
							label={__('Search Ski Resort')}
							value={resortFind}
							onChange={(value) => setAttributes({ resortFind: value })}
						/>

						<PanelRow>
							<div style={{ "height": "auto", "width": "100%", "backgroundColor": "#c2c2c2", padding: "5px" }}>
								{mySuggession ?
									<p>Name: {mySuggession.name}<br />
									   Description: {mySuggession.description}<br />
									   Lift Open: {JSON.stringify(mySuggession.lifts)}	<br />
									   ID: {mySuggession._id}	<br />
									   Type: {mySuggession._type}						
									</p>
									: ''}
							</div>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
								
				<ComboboxControl
					label="Type here to autocomplete Ski-Resort:"
					value={selectedOption}
					// options={ options.map((item) => ({
					// 	value: item.site_path,   // Use `site_path` as the value
					// 	label: item.name        // Use `name` as the label
					//   }))}
					options={options}
					
					onChange={(value) => {
						console.log('Selected Option:', value);
						setAttributes({selectedOption: value})}		
					}
					onFilterValueChange={(inputValue) => {
						setSearch(inputValue); // Update search term when user types
					}}										
				/>
				<div style={{ "height": "auto", "width": "100%", "backgroundColor": "#c2c2c2",}}>
					<img src='https://fnugg.no/app/uploads/sites/89/2024/01/IMG_7270.jpeg' alt='none' width={620}/>
					</div>				
			</p>
		</>
	);
}

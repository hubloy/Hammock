import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';
import { InputUI } from 'ui/admin/form';

import { toast } from 'react-toastify';

export class Members extends Component {

	constructor(props) {
		super(props);

		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
		});

		this.state = {
			activeOption: 0,
			filteredOptions: [],
			showOptions: false,
			userInput: '',
			userId : 0
		};
		this.retrieveDataAsynchronously = this.retrieveDataAsynchronously.bind(this);
	}

	notify(type, message) {
		toast[type](message, {toastId: 'common-members-toast'});
	}

	onClick(e) {
		this.setState({
			activeOption: 0,
			filteredOptions: [],
			showOptions: false,
			userInput: e.currentTarget.innerText,
			userId : e.currentTarget.getAttribute('data-id')
		});
	};

	/**
     * Updates the state of the autocomplete data with the remote data obtained via AJAX.
     * 
     * @param {String} searchText content of the input that will filter the autocomplete data.
     * @return {Nothing} The state is updated but no value is returned
     */
	retrieveDataAsynchronously(searchText) {
		this.fetchWP.get('members/existing_members?search=' + searchText)
			.then((json) => this.setState({
				filteredOptions: json
			}), (err) => this.notify(this.props.hubloy_membership.error, 'error')
			);
	}

	/**
     * Callback triggered when the user types in the autocomplete field
     * 
     * @param {Event} e JavaScript Event
     * @return {Event} Event of JavaScript can be used as usual.
     */
	onChange(e) {
		this.setState({
			activeOption: 0,
			showOptions: true,
			userInput: e.currentTarget.value,
			userId : 0
		});

        /**
         * Handle the remote request with the current text !
         */
		this.retrieveDataAsynchronously(e.target.value);
	}

    /**
     * Callback triggered when the autocomplete input changes.
     * 
     * @param {Object} val Value returned by the getItemValue function.
     * @return {Nothing} No value is returned
     */
	onKeyDown(e) {
		const { activeOption, filteredOptions } = this.state;

		if (e.keyCode === 13) {
			this.setState({
				activeOption: 0,
				showOptions: false,
				userInput: filteredOptions[activeOption]
			});
		} else if (e.keyCode === 38) {
			if (activeOption === 0) {
				return;
			}
			this.setState({ activeOption: activeOption - 1 });
		} else if (e.keyCode === 40) {
			if (activeOption === filteredOptions.length - 1) {
				return;
			}
			this.setState({ activeOption: activeOption + 1 });
		}
	}



	render() {
		const {
			state: { activeOption, filteredOptions, showOptions, userInput, userId }
		} = this;
		let optionList;
		var strings = this.props.hubloy_membership;
		if (showOptions && userInput) {
			var objectLength = Object.keys(filteredOptions).length;
			if ( filteredOptions.length || objectLength ) {
				var userList = filteredOptions;
				if ( typeof filteredOptions === 'object' ) {
					userList = Object.keys(filteredOptions).map(i => filteredOptions[i]);
				}
				optionList = (
					<ul className="hubloy_membership-suggestions">
						{userList.map((user, index) => {
							let className;
							if (index === activeOption) {
								className = 'hubloy_membership-suggestions-active';
							}
							return (
								<li className={className} key={index} data-id={user.id} onClick={this.onClick.bind(this)}>
									{user.name}
								</li>
							);
						})}
					</ul>
				);
			} else {
				optionList = (
					<div className="hubloy_membership-no-suggestions">
						<em>{strings.common.string.search.members.not_found}</em>
					</div>
				);
			}
		}
		return (
			<div className="hubloy_membership-input">
				<InputUI name={`user_id`} type={`hidden`} value={userId} required={true}/>
				<input
					type="text"
					className="uk-input search-box"
					placeholder={strings.common.string.search.members.select}
					onChange={this.onChange.bind(this)}
					onKeyDown={this.onKeyDown.bind(this)}
					value={userInput}
				/>
				{optionList}
			</div>
		);
	}
}
import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';


export default class Table extends Component {

    constructor(props) {
		super(props);
		this.state = {
			pager: {},
			items: [],
			loading : true,
			error : false,
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
		});
	}

    renderRows() {

    }

    render() {
        var columns = this.props.columns;
        var strings = this.props.hammock.strings;
		if ( this.state.loading) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hammock.error}</h3>
				)
			} else {
                this.renderRows();
            }
        }
    }
}
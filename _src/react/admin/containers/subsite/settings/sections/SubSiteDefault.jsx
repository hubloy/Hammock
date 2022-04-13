import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP'

import { toast } from 'react-toastify';

export default class SubSiteDefault extends Component {

    constructor(props) {
        super(props);
		this.save_sub_default_setting = React.createRef();
        this.state = {
            loading : true,
			error : false
        };
        
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
    }

    componentDidMount() {
		const section = this.props.section;
		this.fetchWP.get( 'settings/section?id=' + section )
			.then( (json) => this.setState({
				item : json.form
			}), (err) => console.log( 'error', err )
		);
	}

	notify(message, type) {
		toast[type](message, {toastId: 'site-default-toast'});
	}

    render() {
        const { section, hubloy_membership } = this.props;
        return (
            <div className={"hubloy_membership_default_settings hubloy_membership_default_settings-" + section} id={"hubloy_membership_default_settings-" + section}>{hubloy_membership.no_data}</div>
        )
    }
}
SubSiteDefault.propTypes = {
	hubloy_membership: PropTypes.object
};
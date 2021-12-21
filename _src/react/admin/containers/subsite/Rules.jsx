import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import Dashboard from 'layout/Dashboard'
import Nav from './rules/Nav';
import Table from './rules/Table';

export default class Rules extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var hammock = this.props.hammock,
		    active_nav = this.props.match.params.section !== undefined ? this.props.match.params.section : 'page';
		return (
			<Dashboard hammock={hammock}>
				<div className="uk-child-width-expand hammock-rules" uk-grid="">
                    <div className="hammock-rules-menu uk-width-auto">
                        <Nav hammock={hammock} active_nav={active_nav}/>
                    </div>
                    <div className="hammock-rules-container uk-width-expand">

                    </div>
                </div>
			</Dashboard>
		)
	}
}

Rules.propTypes = {
	hammock: PropTypes.object
};
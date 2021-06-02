import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import fetchWP from '../../../../utils/fetchWP';
import {PaginationUI} from '../../../ui/admin/form';


export default class Table extends Component {

	constructor(props) {
        super(props);
		this.state = {
            pager: {},
            items: [],
			loading : true,
			error : false
        };

        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
    }
    
    async componentDidMount() {
        this.loadPage();
    }

    async componentDidUpdate() {
		if ( typeof this.state.pager.current !== 'undefined' ) {
			this.loadPage();
		}
    }

    loadPage = async () => {
        const page = parseInt( location.hash.split("/").pop() ) || 1;
        if ( page !== this.state.pager.current ) {
            this.load_members();
        }
	}

	load_members = async () => {
		if(typeof this.props.load_counts !== 'undefined' && typeof this.props.load_counts === 'function') {
			var load_counts = this.props.load_counts;
			load_counts();
		}
		const page = parseInt( location.hash.split("/").pop() ) || 1;
		this.fetchWP.get( 'members/list?page=' + page )
			.then( (json) => this.setState({
				items : json.items,
				pager : json.pager,
				loading : false,
				error : false
			}), (err) => this.setState({ loading : false, error : true })
		);
	}
	
	delete(event) {
		event.preventDefault();
		var $this = this,
			$button = jQuery(event.target),
			$btn_txt = $button.text(),
			id = $button.attr('data-id'),
			prompt = $button.attr('data-prompt'),
			helper = window.hammock.helper,
			error = $this.props.hammock.error,
			fetchWP = $this.fetchWP;
		helper.confirm( prompt, 'warning', function() {
			//continue
			$button.attr('disabled', 'disabled');
			$button.html("<div uk-spinner></div>");
			fetchWP.post( 'members/delete', { member : id } )
				.then( (json) => {
					if ( json.status ) {
						helper.notify( json.message, 'success');
						$this.load_members();
					} else {
						helper.notify( json.message, 'warning' );
					}
					$button.removeAttr('disabled');
					$button.html($btn_txt);
				}, (err) => {
					$button.removeAttr('disabled');
					$button.html($btn_txt);
					helper.notify( error, 'error' );
				}
			);
		});
	}

    render() {
        const { pager, items } = this.state;
        var hammock = this.props.hammock;
        var strings = hammock.strings;
        if ( this.state.loading) {
            return (
                <div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
                    <span className="uk-text-center" uk-spinner="ratio: 3"></span>
                </div>
            )
        } else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hammock.error}</h3>
				)
			} else {
				return (
					<React.Fragment>
						{items.length <= 0 ? (
							<div className="infobox">
								<div className="info-icon">
									<span uk-icon="icon: users; ratio: 2"></span>
								</div>
								<h5 className="info-heading">{strings.dashboard.table.not_found}</h5>
								<a className="info-link uk-button uk-button-small" href="#hammock-add-member" uk-toggle="">{strings.dashboard.add_new.button} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
							</div>
						) : (
							<table className="uk-table">
								<thead>
									<tr>
										<th><input className="uk-checkbox hammock-top-checkbox" type="checkbox" /></th>
										<th>{strings.dashboard.table.name}</th>
										<th>{strings.dashboard.table.email}</th>
										<th>{strings.dashboard.table.member_id}</th>
										<th>{strings.dashboard.table.status}</th>
										<th>{strings.dashboard.table.plans}</th>
										<th>{strings.dashboard.table.member_since}</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th><input className="uk-checkbox hammock-bottom-checkbox" type="checkbox" /></th>
										<th>{strings.dashboard.table.name}</th>
										<th>{strings.dashboard.table.email}</th>
										<th>{strings.dashboard.table.member_id}</th>
										<th>{strings.dashboard.table.status}</th>
										<th>{strings.dashboard.table.plans}</th>
										<th>{strings.dashboard.table.member_since}</th>
									</tr>
								</tfoot>
								<tbody>
									{items.map(item =>
										<tr key={item.id}>
											<td><input className="uk-checkbox" type="checkbox" value={item.id} /></td>
											<td>
												<Link className="uk-text-primary" to={"/member/" + item.id}>{item.user_info.name}</Link>
												<div id={"member-hover-"+ item.id}>
													<Link className="uk-text-primary" to={"/member/" + item.id}>{hammock.common.buttons.edit}</Link>{' '}|{' '}<a href="#" data-id={item.id} data-prompt={strings.edit.details.delete.prompt} onClick={this.delete.bind(this)} className="uk-text-danger">{hammock.common.buttons.delete}</a>
												</div>
											</td>
											<td>{item.user_info.email}</td>
											<td><code>{item.member_id}</code></td>
											<td>{item.enabled ? hammock.common.status.enabled : hammock.common.status.disabled}</td>
											<td>{item.plans}</td>
											<td>{item.date_created}</td>
										</tr>
									)}
								</tbody>
							</table>
						)}
						<PaginationUI pager={pager}/>
					</React.Fragment>
				)
			}
        }
    }
}
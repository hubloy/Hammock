import React, { Component } from 'react';

import fetchWP from 'utils/fetchWP';

import {Preloader, Center} from 'ui/admin/form';
import { Bar, Line } from 'react-chartjs-2';

import { toast } from 'react-toastify';

export default class StatsDashboard extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading_subs : true,
			loading_trans : true,
			subscribers : [],
			transactions : []
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	notify(type, message) {
		toast[type](message, {toastId: 'dashboard-stats-toast'});
	}

	componentDidMount() {
		Promise.all([this.load_subscribers(), this.load_transactions()]);
	}

	load_subscribers = async() => {
		this.fetchWP.get( 'dashboard/subscribers' )
			.then( (json) => this.setState({
				subscribers : json,
				loading_subs : false,
				error : false,
			}), (err) => {
				this.setState({ loading_subs : false, error : true });
				this.notify( this.props.hubloy_membership.error, 'error' );
			}
		);
	}

	load_transactions = async() => {
		this.fetchWP.get( 'dashboard/transactions' )
			.then( (json) => this.setState({
				transactions : json,
				loading_trans : false,
				error : false,
			}), (err) => {
				this.setState({ loading_trans : false, error : true });
				this.notify( this.props.hubloy_membership.error, 'error' );
			}
		);
	}


	render() {
		var subscribers = this.state.subscribers;
		var transactions = this.state.transactions;
		var hubloy_membership = this.props.hubloy_membership,
			days = hubloy_membership.strings.dashboard.stats.charts.days;
		const options = {
			scales: {
				yAxes: [
					{
						ticks: {
							beginAtZero: true,
						},
					},
				],
			},
			plugins: {
				legend: {
					onClick: function(e, item) {
						return;
					}
				}
			}
		};
		return (
			<div className="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid="">
				<div className="">
					<div className="uk-padding-small uk-height-large uk-card uk-card-default">
						<div className="uk-card-header uk-padding-remove dashboard-heading">
							<h4 className="uk-card-title">{hubloy_membership.strings.dashboard.stats.title.subscribers}</h4>
						</div>
						<div className="uk-card-body">
							{this.state.loading_subs ? (
								<Preloader />
							) : (
								subscribers.length > 0 ? (
									<Bar data={{
										labels: [days.mon, days.tue, days.wed, days.thu, days.fri, days.sat, days.sun],
										datasets: [
											{
												label: hubloy_membership.strings.dashboard.stats.charts.subscribers,
												data: [subscribers.mon, subscribers.tue, subscribers.wed, subscribers.thu, subscribers.fri, subscribers.sat, subscribers.sun],
												backgroundColor: 'rgb(49, 104, 142)',
												borderColor: 'rgb(49, 104, 142)',
												borderWidth: 1,
											},
										],
									}} options={options}/>
								) : (
									<Center text={hubloy_membership.strings.dashboard.stats.no_data.subscribers} backgroundImage={hubloy_membership.assets_url + '/img/preloader-chart.jpg'}/>
								)
							)}
						</div>
					</div>
				</div>
				<div className="">
					<div className="uk-padding-small uk-height-large uk-card uk-card-default">
						<div className="uk-card-header uk-padding-remove dashboard-heading">
							<h4 className="uk-card-title">{hubloy_membership.strings.dashboard.stats.title.transactions}</h4>
						</div>
						<div className="uk-card-body">
							{this.state.loading_trans ? (
								<Preloader />
							) : (
								transactions.length > 0 ? (
									<Line data={{
										labels: [days.mon, days.tue, days.wed, days.thu, days.fri, days.sat, days.sun],
										datasets: [
											{
												label: hubloy_membership.strings.dashboard.stats.charts.transactions,
												data: [transactions.mon, transactions.tue, transactions.wed, transactions.thu, transactions.fri, transactions.sat, transactions.sun],
												fill: false,
												backgroundColor: 'rgb(49, 104, 142)',
												borderColor: 'rgba(49, 104, 142)',
											},
										],
									}} options={options} />
								) : (
									<Center text={hubloy_membership.strings.dashboard.stats.no_data.transactions} backgroundImage={hubloy_membership.assets_url + '/img/preloader-chart.jpg'}/>
								)
							)}
						</div>
					</div>
				</div>
			</div>
		)
	}
}
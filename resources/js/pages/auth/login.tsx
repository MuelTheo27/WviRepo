/* eslint-disable @typescript-eslint/no-unused-vars */
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useEffect } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
};

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

 
    return (
        <div style={{ fontFamily: 'sans-serif', backgroundColor: '#f9fafb' }}>
            <Head title="Login" />

            <div style={{
                minHeight: '100vh',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                padding: '0 1rem'
            }}>
                <div style={{
                    display: 'grid',
                    gridTemplateColumns: window.innerWidth >= 768 ? '1fr 1fr' : '1fr',
                    alignItems: 'center',
                    gap: '1rem',
                    maxWidth: '72rem',
                    width: '100%',
                    padding: '1rem',
                    margin: '1rem',
                    boxShadow: '0 2px 10px -3px rgba(255,165,0,0.3)',
                    borderRadius: '0.375rem',
                    backgroundColor: 'white'
                }}>

                    {/* Form Section */}
                    <div style={{
                        maxWidth: window.innerWidth >= 768 ? '28rem' : '100%',
                        width: '100%',
                        padding: '1rem'
                    }}>
                        {status && (
                            <div style={{
                                marginBottom: '1rem',
                                color: '#16a34a',
                                fontWeight: 600
                            }}>
                                {status}
                            </div>
                        )}

                        <form onSubmit={submit}>
                            <div style={{ marginBottom: '2.5rem' }}>
                                <h3 style={{
                                    color: '#1f2937',
                                    fontSize: '1.875rem',
                                    fontWeight: 800
                                }}>Sign in</h3>
                                <p style={{
                                    fontSize: '0.875rem',
                                    marginTop: '0.75rem',
                                    color: '#374151'
                                }}>
                                    Don't have an account?
                                    <TextLink
                                        href={route('register')}
                                        style={{
                                            color: '#f97316',
                                            fontWeight: 600,
                                            marginLeft: '0.25rem'
                                        }}
                                    >
                                        Register here
                                    </TextLink>
                                </p>
                            </div>

                            {/* Email */}
                            <div style={{ marginBottom: '2rem' }}>
                                <Label
                                    htmlFor="email"
                                    style={{
                                        color: '#1f2937',
                                        fontSize: '0.75rem',
                                        display: 'block',
                                        marginBottom: '0.5rem'
                                    }}
                                >
                                    Email
                                </Label>
                                <div style={{ }}>
                                    <Input
                                        id="email"
                                        type="email"
                                        required
                                        autoFocus
                                        style={{
                                            width: '100%',
                                            color: '#1f2937',
                                            fontSize: '0.875rem',

                                            border: '0px',
                                            paddingLeft: '0.5rem',
                                            paddingRight: '2rem',
                                            paddingTop: '0.75rem',
                                            paddingBottom: '0.75rem',
                                            outline: 'none'
                                        }}
                                        placeholder="Enter email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                    />
                                    <InputError
                                        message={errors.email}
                                        style={{
                                            color: '#ef4444',
                                            fontSize: '0.75rem',
                                            position: 'absolute',
                                            // marginTop :'1px'
                                        }}
                                    />
                                </div>
                            </div>

                            {/* Password */}
                            <div style={{ marginBottom: '2rem' }}>
                                <Label
                                    htmlFor="password"
                                    style={{
                                        color: '#1f2937',
                                        fontSize: '0.75rem',
                                        display: 'block',
                                        marginBottom: '0.5rem'
                                    }}
                                >
                                    Password
                                </Label>
                                <div style={{ }}>
                                    <Input
                                        id="password"
                                        type="password"
                                        required
                                        style={{
                                            width: '100%',
                                            color: '#1f2937',
                                            fontSize: '0.875rem',
                                            border: '0px',
                                            paddingLeft: '0.5rem',
                                            paddingRight: '2rem',
                                            paddingTop: '0.75rem',
                                            paddingBottom: '0.75rem',
                                            outline: 'none'
                                        }}
                                        placeholder="Enter password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                    />
                                    <InputError
                                        message={errors.password}
                                        style={{
                                            color: '#ef4444',
                                            fontSize: '0.75rem',
                                            position: 'absolute',
                                      
                                        }}
                                    />
                                </div>
                            </div>

                            {/* Remember Me & Forgot Password */}
                            <div style={{
                                display: 'flex',
                                justifyContent: 'space-between',
                                alignItems: 'center',
                                marginTop: '1.5rem'
                            }}>
                                <div style={{ display: "flex", alignItems: "center", gap: "0.75rem" }}>
                                    <div style={{ position: 'relative', height: '1rem', width: '1rem' }}>
                                        <input
                                            type="checkbox"
                                            id="remember"
                                            name="remember"
                                            checked={data.remember}
                                            onChange={() => setData('remember', !data.remember)}
                                            style={{
                                                height: '1rem',
                                                width: '1rem',
                                                appearance: 'none',
                                                backgroundColor: data.remember ? '#f97316' : 'white',
                                                border: '1px solid #d1d5db',
                                                borderRadius: '0.25rem',
                                                cursor: 'pointer'
                                            }}
                                        />
                                        {data.remember && (
                                            <svg
                                                style={{
                                                    position: 'absolute',
                                                    top: '0',
                                                    left: '0',
                                                    height: '1rem',
                                                    width: '1rem',
                                                    color: 'white',
                                                    pointerEvents: 'none'
                                                }}
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                strokeWidth="2"
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                            >
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        )}
                                    </div>
                                    <Label
                                        htmlFor="remember"
                                        style={{
                                            marginLeft: '0.5rem',
                                            fontSize: '0.875rem',
                                            color: '#1f2937'
                                        }}
                                    >
                                        Remember me
                                    </Label>
                                </div>
                                {canResetPassword && (
                                    <TextLink
                                        href={route('password.request')}
                                        style={{
                                            color: '#f97316',
                                            fontWeight: 600,
                                            fontSize: '0.875rem'
                                        }}
                                    >
                                        Forgot Password?
                                    </TextLink>
                                )}
                            </div>

                            {/* Submit Button */}
                            <div style={{ marginTop: '2.5rem' }}>
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    style={{
                                        width: '100%',
                                        paddingTop: '0.625rem',
                                        paddingBottom: '0.625rem',
                                        paddingLeft: '1rem',
                                        paddingRight: '1rem',
                                        fontSize: '0.875rem',
                                        border: '0px',
                                        borderRadius: '0.375rem',
                                        color: 'white',
                                        backgroundColor: '#f97316',
                                        outline: 'none',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        cursor: processing ? 'not-allowed' : 'pointer'
                                    }}
                                >
                                    {processing && (
                                        <svg
                                            style={{
                                                animation: 'spin 1s linear infinite',
                                                marginRight: '0.5rem',
                                                height: '1rem',
                                                width: '1rem'
                                            }}
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle style={{ opacity: 0.25 }} cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                            <path style={{ opacity: 0.75 }} fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    )}
                                    Sign in
                                </Button>
                            </div>
                        </form>
                    </div>

                    {/* Image Section */}
                    <div style={{
                        display: window.innerWidth >= 768 ? 'flex' : 'none',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }}>
                        {/* <img 
                            src="https://www.worldvision.org.ph/wp-content/uploads/2024/11/Photo-10-28-24-8-58-37-AM-scaled.jpg" 
                            alt="Login Illustration" 
                            style={{ borderRadius: '0.375rem' }}
                        /> */}
                    </div>
                </div>
            </div>
        </div>
    );
}
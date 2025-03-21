/* eslint-disable @typescript-eslint/no-unused-vars */
import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TextLink from '@/components/text-link';
import "../../../css/app.css";

type RegisterForm = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
};

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (

        <div style={{
            backgroundColor: "#fff7ed",
            minHeight: "100vh",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            padding: "2rem 1rem",
            fontFamily: "sans-serif"
        }}>
            <div style={{
                maxWidth: "28rem",
                width: "100%",
                backgroundColor: "#ffffff",
                borderRadius: "1rem",
                boxShadow: "0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)",
                border: "1px solid #fed7aa",
                padding: "2rem"
            }}>

                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    marginBottom: "1.5rem"
                }}>
                    <img
                        src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/World_Vision_new_logo.png/1200px-World_Vision_new_logo.png"
                        alt="Logo"
                        style={{
                            width: "7rem",
                            height: "auto",
                            boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)"
                        }}
                    />
                </div>
                <h2 style={{
                    fontSize: "1.5rem",
                    fontWeight: "700",
                    color: "#c2410c",
                    textAlign: "center",
                    marginBottom: "1rem"
                }}>Create an account</h2>
                <form onSubmit={submit} style={{ marginTop: "1rem" }}>

                    <div style={{ marginBottom: "1rem" }}>
                        <label style={{
                            display: "block",
                            color: "#9a3412",
                            fontSize: "0.875rem",
                            fontWeight: "600",
                            marginBottom: "0.5rem"
                        }}>Name</label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            disabled={processing}
                            placeholder="Enter your name"
                            style={{
                                width: "100%",
                                color: "#7c2d12",
                                border: "1px solid #fdba74",
                                padding: "0.75rem 1rem",
                                fontSize: "0.875rem"
                            }}
                        />
                    
                        {/* @error('name')
        <p style={{color: "#ef4444", fontSize: "0.875rem", marginTop: "0.5rem"}}>{{ $message }}</p>
        @enderror */}
                    </div>

                    <div style={{ marginBottom: "1rem" }}>
                        <label style={{
                            display: "block",
                            color: "#9a3412",
                            fontSize: "0.875rem",
                            fontWeight: "600",
                            marginBottom: "0.5rem"
                        }}>Email</label>
                        <Input
                            id="email"
                            type="text"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            disabled={processing}
                            placeholder="Enter your email"
                            style={{
                                width: "100%",
                                color: "#7c2d12",
                                border: "1px solid #fdba74",
                                padding: "0.75rem 1rem",
                                fontSize: "0.875rem"
                            }}
                        />
                     
                        {/* @error('email')
        <p style={{color: "#ef4444", fontSize: "0.875rem", marginTop: "0.5rem"}}>{{ $message }}</p>
        @enderror */}
                    </div>

                    <div style={{ marginBottom: "1rem" }}>
                        <label style={{
                            display: "block",
                            color: "#9a3412",
                            fontSize: "0.875rem",
                            fontWeight: "600",
                            marginBottom: "0.5rem"
                        }}>Password</label>
                         <Input
                            id="password"
                            type="password"
                            required
                            tabIndex={3}
                            autoComplete="new-password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            disabled={processing}
                            placeholder="Enter your password"
                            style={{
                                width: "100%",
                                color: "#7c2d12",
                                border: "1px solid #fdba74",
                                padding: "0.75rem 1rem",
                                fontSize: "0.875rem"
                            }}
                        />
                       
                        {/* @error('password')
        <p style={{color: "#ef4444", fontSize: "0.875rem", marginTop: "0.5rem"}}>{{ $message }}</p>
        @enderror */}
                    </div>

                    <div style={{ marginBottom: "1rem" }}>
                        <label style={{
                            display: "block",
                            color: "#9a3412",
                            fontSize: "0.875rem",
                            fontWeight: "600",
                            marginBottom: "0.5rem"
                        }}>Confirm Password</label>
                           <Input
                            id="password_confirmation"
                            type="password"
                            required
                            tabIndex={4}
                            autoComplete="new-password"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            disabled={processing}
                            placeholder="Confirm password"
                            style={{
                                width: "100%",
                                color: "#7c2d12",
                                border: "1px solid #fdba74",
                                padding: "0.75rem 1rem",
                                fontSize: "0.875rem"
                            }}
                        />
                       
                        {/* @error('password_confirmation')
        <p style={{color: "#ef4444", fontSize: "0.875rem", marginTop: "0.5rem"}}>{{ $message }}</p>
        @enderror */}
                    </div>

                    <div style={{
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "space-between",
                        marginTop: "1.5rem"
                    }}>
                        <div   style={{
                                fontSize: "0.875rem",
                                color: "#ea580c",
                                fontWeight: "600"
                            }}>
                  
                    <TextLink href={route('login')} tabIndex={6}>
                    Already have an account?
                    </TextLink>
                </div>
                        {/* <a
                            href="{{ route('login') }}"
                          
                        >
                            Already registered?
                        </a> */}
                        <Button type="submit" style={{
                                padding: "0.75rem 1.5rem",
                                fontSize: "0.875rem",
                                fontWeight: "600",
                                borderRadius: "0.5rem",
                                color: "#ffffff",
                                backgroundColor: "#f97316",
                                transition: "background-color 0.2s",
                                border : "0px"
                            }} tabIndex={5} disabled={processing}>
                            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                            Register
                        </Button>
                       
                    </div>
                </form>
            </div></div>

    );
}